from graphene import *
from flask import Flask, render_template, request
from flask_graphql import GraphQLView
from graphql import graphql
import requests as req
import json
from flask_cors import CORS
import mysql.connector
import os
from zeep import Client

DB_CONFIG = {
    'host': 'host.docker.internal',
    'user': 'root',
    'password': '',
    'database': 'finaletaakcloudcomputing',
    'port': 3306
}

SOAP_WSDL_URL = "http://soap-service:8080/ws/football.wsdl"

def get_db_connection():
    return mysql.connector.connect(**DB_CONFIG)

def get_team_stats_via_soap(team_name):
    client = Client(SOAP_WSDL_URL)
    response = client.service.getTeamStats(teamName=team_name)
    return response

class Match(ObjectType):
    """Een voetbalwedstrijd tussen twee teams"""
    datum = Field(String, required=True, description="Datum van de wedstrijd (GMT)")
    thuisploeg = Field(lambda: Team, required=True, description="Het thuisspelende team")   
    uitploeg = Field(lambda: Team, required=True, description="Het uitspelende team")
    scheidsrechter = Field(String, required=True, description="Naam van de scheidsrechter")
    score = Field(lambda: Score, required=True, description="Eindstand en statistieken")
    winnaar = Field(lambda: Team, description="Het winnende team (None bij gelijkspel)")           
    aantal_bezoekers = Field(Int, description="Aantal toeschouwers")
    stadion = Field(String, required=True, description="Naam van het stadion")
    
class Score(ObjectType):
    """Scoreinformatie van een wedstrijd"""
    thuisploeg_doelpunten = Field(Int, required=True, description="Doelpunten thuisteam")
    uitploeg_doelpunten = Field(Int, required=True, description="Doelpunten uitteam")
    thuisploeg_tijdstippen_doelpunten = Field(List(String), description="Minuten van doelpunten thuisteam")
    uitploeg_tijdstippen_doelpunten = Field(List(String), description="Minuten van doelpunten uitteam")
    thuisploeg_verwachte_doelpunten = Field(Float, description="Expected Goals (xG) thuisteam")
    uitploeg_verwachte_doelpunten = Field(Float, description="Expected Goals (xG) uitteam")

class Team(ObjectType):
    """Een voetbalteam met statistieken"""
    id = Field(Int, required=True, description="TeamID voor de database")
    naam = Field(String, required=True, description="Teamnaam")
    wedstrijden_gespeeld = Field(Int, description="Aantal gespeelde wedstrijden")
    wedstrijden_gewonnen = Field(Int, description="Aantal gewonnen wedstrijden")
    wedstrijden_gewonnen_thuis = Field(Int, description="Aantal gewonnen wedstrijden thuis")
    wedstrijden_gewonnen_uit = Field(Int, description="Aantal gewonnen wedstrijden uit")
    wedstrijden_verloren = Field(Int, description="Aantal verloren wedstrijden")
    wedstrijden_verloren_thuis = Field(Int, description="Aantal verloren wedstrijden thuis")
    wedstrijden_verloren_uit = Field(Int, description="Aantal verloren wedstrijden uit")
    wedstrijden_gelijkspel = Field(Int, description="Aantal gelijkspel wedstrijden")
    gem_punten_per_match = Field(Float, description="Gemiddeld aantal punten per wedstrijd")
    eindplaats = Field(Int, required=True, description="Eindpositie in de competitie")
    doelpunten_gemaakt = Field(Int, description="Totaal aantal gescoorde doelpunten")
    doelpunten_tegen = Field(Int, description="Totaal aantal tegen doelpunten")
    spelers = Field(List(lambda: Player), description="Lijst van spelers in het team")

    # De speler van de teams aanmaken
    def resolve_spelers(parent, info):
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)

        query = "SELECT * FROM players WHERE team_id = %s"
        cursor.execute(query, (parent.id,))
        player_rows = cursor.fetchall()
        cursor.close()
        connection.close()
        return [maakPlayer(row) for row in player_rows]

    
    # De matches van een team aanmaken
    def resolve_matches(parent, info, limit=None): 
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)

        query = "SELECT * FROM matches WHERE home_team_id = %s OR away_team_id = %s"
        cursor.execute(query, (parent.id, parent.id))
        matches_rows = cursor.fetchall()
        cursor.close()
        connection.close()
        return [maakMatch(row) for row in matches_rows if row]

class Player(ObjectType):
    """Een voetbalspeler met carrièrestatistieken"""
    naam = Field(String, required=True, description="Volledige naam van de speler")
    leeftijd = Field(Int, description="Leeftijd van de speler")
    geboorte_datum = Field(String, description="Geboortedatum van de speler")
    positie = Field(String, required=True, description="Speelpositie van de speler")
    club = Field(Team, description="Huidige club van de speler")
    minuten_gespeeld = Field(Int, description="Totaal aantal gespeelde minuten")
    nationaliteit = Field(String, description="Nationaliteit van de speler")
    aantal_doelpunten = Field(Int, description="Totaal aantal gescoorde doelpunten")
    aantal_assisten = Field(Int, description="Totaal aantal assists")
    aantal_gele_kaarten = Field(Int, description="Totaal aantal gele kaarten")
    aantal_rode_kaarten = Field(Int, description="Totaal aantal rode kaarten")

# Hulp van Copilot (-->bronvermelding)
def formatTijdstippen(string):
    """Zet tijdstippen om naar 2-cijferige getallen"""
    if not string or string == "":
        return []

    tijdstippen = []
    for tijd in str(string).split(","):
        tijd = tijd.strip()
        if tijd and tijd != "":
            try:
                minuut = int(tijd)
                tijdstippen.append(f"{minuut:02d}")
            except ValueError:
                tijdstippen.append(tijd)
    return tijdstippen

def get_team_by_id(team_id):
    connection = get_db_connection()
    cursor = connection.cursor(dictionary=True)
    
    query = "SELECT * FROM teams WHERE id = %s"

    cursor.execute(query, (team_id,))
    team_row = cursor.fetchone()
    cursor.close()
    connection.close()

    if team_id : return maakTeam(team_row)

    return None

def maakMatch(row):
    if not row : return None
    return Match(
        datum = row['date_gmt'],
        thuisploeg = get_team_by_id(row['home_team_id']),
        uitploeg = get_team_by_id(row['away_team_id']),
        scheidsrechter = row['referee'],
        score = maakScore(row),
        winnaar = maakWinnaar(row),
        aantal_bezoekers = row['attendance'],
        stadion = row['stadium_name']
    )

def maakScore(row):
    return Score(
            thuisploeg_doelpunten = row['home_team_goal_count'],
            uitploeg_doelpunten = row['away_team_goal_count'],
            # thuisploeg_tijdstippen_doelpunten = formatTijdstippen(row['home_team_goal_timings']),
            # uitploeg_tijdstippen_doelpunten =formatTijdstippen(row['away_team_goal_timings']),
            # thuisploeg_verwachte_doelpunten = row['team_a_xg'],
            # uitploeg_verwachte_doelpunten = row['team_b_xg'],
    )

def maakWinnaar(row):
    if row['home_team_goal_count'] > row['away_team_goal_count']:
        return get_team_by_id(row['home_team_id'])
    elif row['away_team_goal_count'] > row['home_team_goal_count']:
        return get_team_by_id(row['away_team_id'])
    else:
        return None


def maakTeam(row):
    if not row : return None
    return Team(
            id=row['id'],
            naam = row['common_name'],
            wedstrijden_gespeeld = row['matches_played'],
            wedstrijden_gewonnen = row['wins'],
            wedstrijden_gewonnen_thuis = row['wins_home'],
            wedstrijden_gewonnen_uit = row['wins_away'],
            wedstrijden_verloren = row['losses'],
            wedstrijden_verloren_thuis = row['losses_home'],
            wedstrijden_verloren_uit = row['losses_away'],
            wedstrijden_gelijkspel = row['draws'],
            gem_punten_per_match = row['points_per_game'],
            eindplaats = row['league_position'],
            doelpunten_gemaakt = row['goals_scored'],
            doelpunten_tegen = row['goals_conceded']
    )

def maakPlayer(row):
    if not row:
        return None
    return Player(
                naam = row['full_name'],
                leeftijd = row['age'],
                geboorte_datum = row['birthday_GMT'],
                positie = row['position'],
                club = get_team_by_id(row['team_id']),
                minuten_gespeeld = row['minutes_played_overall'],
                nationaliteit = row['nationality'],
                aantal_doelpunten = row['goals_overall'],
                aantal_assisten = row['assists_overall'],
                aantal_gele_kaarten = row['yellow_cards_overall'],
                aantal_rode_kaarten = row['red_cards_overall']
    )


class Query(ObjectType):
    """Root query voor alle beschikbare zoekopdrachten"""
    speler = Field(Player, name=Argument(String, description="Volledige naam van de speler"), description="Zoek een speler op naam")
    team = Field(Team, name=Argument(String, required=True, description="Naam van het team"), description="Statistieken van een specifiek team ophalen")
    team_matches = List(Match, team_name=Argument(String, required=True, description="Naam van het team"), limit=Argument(Int, description="Maximaal aantal resultaten"), offset=Argument(Int, default_value=0, description="Aantal over te slaan"), description="Alle wedstrijden van een specifiek team")

    def resolve_speler(parent, info, name):
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)

        query = "SELECT * FROM players WHERE full_name = %s"

        cursor.execute(query, (name,))
        player_row = cursor.fetchone()
        cursor.close()
        connection.close()
        return maakPlayer(player_row)
    
    def resolve_spelers(parent, info):
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)

        query = "SELECT * FROM players WHERE 'Current club' = %s"

        cursor.execute(query, (parent.naam,))
        player_rows = cursor.fetchall()

        return [Player(
            naam=r['full_name'],
            leeftijd=r['age'],
            positie=r['position'],
        ) for r in player_rows]
    
    def resolve_team(parent, info, name):
        soap_response = get_team_stats_via_soap(name)

        if soap_response:
            return Team(
                id=soap_response.id,
                naam=soap_response.common_name,
                wedstrijden_gespeeld=soap_response.matches_played,
                wedstrijden_gewonnen=soap_response.wins,
                wedstrijden_gewonnen_thuis=soap_response.wins_home,
                wedstrijden_gewonnen_uit=soap_response.wins_away,
                wedstrijden_verloren=soap_response.losses,
                wedstrijden_verloren_thuis=soap_response.losses_home,
                wedstrijden_verloren_uit=soap_response.losses_away,
                wedstrijden_gelijkspel=soap_response.draws,
                gem_punten_per_match=soap_response.points_per_game,
                eindplaats=soap_response.league_position,
                doelpunten_gemaakt=soap_response.goals_scored,
                doelpunten_tegen=soap_response.goals_conceded,
            )
        return None
    
    
    def resolve_team_matches(parent, info, team_name, limit=None, offset=0):
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)
        
        # Eerst team_id ophalen
        cursor.execute("SELECT id FROM teams WHERE common_name = %s", (team_name,))
        team = cursor.fetchone()
        
        if not team:
            cursor.close()
            connection.close()
            return []
        
        team_id = team['id']
        query = "SELECT * FROM matches WHERE home_team_id = %s OR away_team_id = %s LIMIT %s OFFSET %s"
        cursor.execute(query, (team_id, team_id, limit or 100, offset))
        matches_rows = cursor.fetchall()
        
        cursor.close()
        connection.close()
        return [maakMatch(row) for row in matches_rows if row]


schema = Schema(query=Query)

myWebApp = Flask("My App")
CORS(myWebApp)

@myWebApp.route("/")
def hello_world():
    return render_template("index.html")

@myWebApp.route("/api/matches")
def api_matches():
    team = request.args.get('team', None) 
    
    if team:
        # Hier al een query ingeven om deze te verwerken
        # Zowel zoeken op de matches een team als de statistieken van een team
        query_string = f"""
        {{
            team(name: "{team}") {{
                naam
                eindplaats
                wedstrijdenGespeeld
                wedstrijdenGewonnen
                wedstrijdenGewonnenThuis
                wedstrijdenGewonnenUit
                wedstrijdenVerloren
                wedstrijdenVerlorenThuis
                wedstrijdenVerlorenUit
                wedstrijdenGelijkspel
                gemPuntenPerMatch
                doelpuntenGemaakt
                doelpuntenTegen
                spelers {{
                    naam
                    leeftijd
                    positie
                    minutenGespeeld
                }}
            }}
            teamMatches(teamName: "{team}", limit: 100) {{
                datum
                stadion
                aantalBezoekers
                scheidsrechter
                thuisploeg {{
                    naam
                    eindplaats
                }}
                uitploeg {{
                    naam
                    eindplaats
                }}
                score {{
                    thuisploegDoelpunten
                    uitploegDoelpunten
                }}
            }}
        }}
        """
        result = graphql(schema, query_string)
        if result.errors:
            return json.dumps({"errors": [str(e) for e in result.errors]})
        return json.dumps({
            "team": result.data.get("team"),
            "matches": result.data.get("teamMatches", [])    # De [] zorgt ervoor dat bij afwezige teamMatches de API een lege lijst teruggeeft 
        })
    else:
        return json.dumps({"error": "No team specified. Please provide a team name using ?team=TeamName"})

myWebApp.add_url_rule('/graphiql',
                        view_func=GraphQLView.as_view('graphql',
                        schema=schema,
                        graphiql=True)
                    )