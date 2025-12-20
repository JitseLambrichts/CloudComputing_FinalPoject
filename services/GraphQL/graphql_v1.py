from graphene import *
from flask import Flask, render_template, request
from flask_graphql import GraphQLView
from graphql import graphql
import pandas as pd
import requests as req
import json
from flask_cors import CORS

dfLeague = pd.read_csv("league_stats.csv", sep=",", encoding="latin-1")
dfMatches = pd.read_csv("matches.csv", sep=",", encoding="latin-1")
dfPlayers = pd.read_csv("players.csv", sep=",", encoding="latin-1")
dfTeams = pd.read_csv("teams.csv", sep=",", encoding="latin-1")

dfLeague['id'] = dfLeague.index
dfMatches['id'] = dfMatches.index
dfPlayers['id'] = dfPlayers.index
dfTeams['id'] = dfTeams.index

class League(ObjectType):
    """Een voetbalcompetitie met statistieken over een seizoen"""
    naam = Field(String, required=True, description="Naam van de competitie")
    seizoen = Field(String, required=True, description="Seizoen (bijv. 2023/2024)")
    aantal_matches = Field(Int, description="Totaal aantal gespeelde wedstrijden")
    gem_doelpunten = Field(Int, description="Gemiddeld aantal doelpunten per wedstrijd")
    matches = Field(List(lambda: Match), 
                    limit = Argument(Int, description="Maximaal aantal wedstrijden om weer te geven"),
                    offset = Argument(Int, default_value=0, description="Aantal matches over te slaan"),
                    description="Alle wedstrijden in deze competitie")

    # Alle matchen voor de league
    def resolve_matches(parent, info, limit=None, offset=0):
        all_matches = [maakMatch(row) for index, row in dfMatches.iterrows()]
        if limit:
            return all_matches[offset: offset + limit]
        return all_matches[offset:]

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
        team_spelers = dfPlayers[dfPlayers['Current Club'] == parent.naam]
        return [maakPlayer(speler_id) for speler_id in team_spelers['id']]

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

def maakLeague(row):
    return League(
        naam = row['name'],
        seizoen = row['season'],
        aantal_matches = row['total_matches'],
        gem_doelpunten = row['average_goals_per_match']
        # Geen matches "aanmaken" want GraphQL gaat automatisch de resolve_matches oproepen
    )

def maakMatch(row):
    return Match(
        datum = row['date_GMT'],
        thuisploeg = maakTeam(dfTeams[dfTeams['common_name']==row['home_team_name']].iloc[0]),
        uitploeg = maakTeam(dfTeams[dfTeams['common_name']==row['away_team_name']].iloc[0]),
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
            thuisploeg_tijdstippen_doelpunten = formatTijdstippen(row['home_team_goal_timings']),
            uitploeg_tijdstippen_doelpunten =formatTijdstippen(row['away_team_goal_timings']),
            thuisploeg_verwachte_doelpunten = row['team_a_xg'],
            uitploeg_verwachte_doelpunten = row['team_b_xg'],
    )

def maakWinnaar(row):
    if row['home_team_goal_count'] > row['away_team_goal_count']:
        return maakTeam(dfTeams[dfTeams['common_name']==row['home_team_name']].iloc[0])
    elif row['away_team_goal_count'] > row['home_team_goal_count']:
        return maakTeam(dfTeams[dfTeams['common_name']==row['away_team_name']].iloc[0]) 
    else:
        return None


def maakTeam(row):
    return Team(
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

def maakPlayer(id):
    speler = dfPlayers.iloc[id]

    return Player(
                naam = speler['full_name'],
                leeftijd = speler['age'],
                geboorte_datum = speler['birthday_GMT'],
                positie = speler['position'],
                club = maakTeam(dfTeams[dfTeams['common_name']==speler['Current Club']].iloc[0]),
                minuten_gespeeld = speler['minutes_played_overall'],
                nationaliteit = speler['nationality'],
                aantal_doelpunten = speler['goals_overall'],
                aantal_assisten = speler['assists_overall'],
                aantal_gele_kaarten = speler['yellow_cards_overall'],
                aantal_rode_kaarten = speler['red_cards_overall']
    )


class Query(ObjectType):
    """Root query voor alle beschikbare zoekopdrachten"""
    speler = Field(Player, name=Argument(String, description="Volledige naam van de speler"), description="Zoek een speler op naam")
    league = Field(League, id=Argument(Int, default_value=0, description="ID van de competitie"), description="Haal competitiegegevens op")
    team = Field(Team, name=Argument(String, required=True, description="Naam van het team"), description="Statistieken van een specifiek team ophalen")
    matches = List(Match, limit=Argument(Int, description="Maximaal aantal resultaten"), offset=Argument(Int, default_value=0, description="Aantal over te slaan"), description="Alle wedstrijden ophalen")
    team_matches = List(Match, team_name=Argument(String, required=True, description="Naam van het team"), limit=Argument(Int, description="Maximaal aantal resultaten"), offset=Argument(Int, default_value=0, description="Aantal over te slaan"), description="Alle wedstrijden van een specifiek team")

    def resolve_speler(parent, info, name):
        player_row = dfPlayers[dfPlayers["full_name"] == name]
        if not player_row.empty:
            return maakPlayer(player_row.iloc[0]['id'])
        return None
    
    def resolve_spelers(parent, info):
        return [maakPlayer(speler_id) for speler_id in dfPlayers['id']]
    
    def resolve_league(parent, info, id):
        return maakLeague(dfLeague[dfLeague['id']==id].iloc[0])
    
    def resolve_team(parent, info, name):
        team_row = dfTeams[dfTeams['common_name'] == name]
        if not team_row.empty:
            return maakTeam(team_row.iloc[0])
        return None
    
    def resolve_matches(parent, info, limit=None, offset=0):
        rows = dfMatches.iloc[offset: (offset + limit) if limit else None]  # Hier wordt een offset gebruik om een aantal matches te kunnen opvragen vanaf een aantal (bv. de laatste 5 matches van een seizoen opvragen)
        return [maakMatch(row) for index, row in rows.iterrows()]
    
    def resolve_team_matches(parent, info, team_name, limit=0, offset=0):
        team_matches = dfMatches[
            (dfMatches['home_team_name'] == team_name) |
            (dfMatches['away_team_name'] == team_name)
        ]

        rows = team_matches.iloc[offset: (offset + limit) if limit else None]
        return [maakMatch(row) for index, row in rows.iterrows()]


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
        # Anders de eerste 5 matchen laten zien
        query_string = """
        {
          matches(limit: 5) {
            datum
            thuisploeg {
              naam
              wedstrijdenGespeeld
              wedstrijdenGewonnen
              eindplaats
              doelpuntenGemaakt
              doelpuntenTegen
            }
            uitploeg {
              naam
              wedstrijdenGespeeld
              wedstrijdenGewonnen
              eindplaats
              doelpuntenGemaakt
              doelpuntenTegen
            }
            score {
              thuisploegDoelpunten
              uitploegDoelpunten
            }
          }
        }
        """
        result = graphql(schema, query_string)
        if result.errors:
            return json.dumps({"errors": [str(e) for e in result.errors]})
        return json.dumps({"matches": result.data.get("matches", [])}, default=str)

myWebApp.add_url_rule('/graphiql',
                        view_func=GraphQLView.as_view('graphql',
                        schema=schema,
                        graphiql=True)
                    )