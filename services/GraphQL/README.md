# Service - GraphQL (Python)

## Teams
Deze service maakt het mogelijk om teams op te vragen, met hun bijbehorende statistieken. Op de pagina "Teams" kan je teams opzoeken uit de Premier League van seizoen 2018-2019 (zie bijlage A).

Deze naam wordt dan meegegeven aan GraphQL, en deze zal dan met deze 'index' de data van het welbepaalde team ophalen (dankzij SOAP -> zie documentatie SOAP).
De query die achterliggend wordt uitgevoerd (zie bijlage B) kan opgedeeld worden in 2 delen. Het eerste deel haalt voor het welbepaalde team de teamstatistieken op voor het seizoen (winstmatchen, doelpunten, spelers, ...). Het 2de deel haalt alle matchen van het seizoen (met hun bijbehorende data zoals score, verwachte doelpunten, ...).

## Spelers
Bij het resultaat van de teams worden ook de spelers van het team getoond. Als een speler geselecteerd wordt, dan zal er naar de Player-pagina gegaan worden. Op deze pagina staan de spelersstatistieken van dit seizoen. Deze worden ook opgevraagd door de GraphQL-service (voor query zie bijlage C). Als de MQTT-service afloopt zal er ook een mutation plaatsvinden die dan 90 min. bij de speler in kwestie optelt.

Endpoint: `http://localhost:5001`
- GraphiQL: `/graphiql`
- Teams API: `/api/matches`
- Player API: `/api/player`

Deze data is afkomstig van een online gevonden dataset (bron: https://footystats.org/download-stats-csv#).

# Bijlage
### Bijlage A
- Arsenal
- Tottenham Hotspur
- Manchester City
- Leicester City
- Crystal Palace
- Everton
- Burnley
- Southampton
- AFC Bournemouth
- Manchester United
- Liverpool
- Chelsea
- West Ham United
- Watford
- Newcastle United
- Cardiff City
- Fulham
- Brighton & Hove Albion
- Huddersfield Town
- Wolverhampton Wanderers

### Bijlage B
```
Achterliggende query team
{
    team(name: "{team}") {
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
        spelers {
            naam
            leeftijd
            positie
            minutenGespeeld
        }
    }
    teamMatches(teamName: "{team}", limit: 100) {
        datum
        stadion
        aantalBezoekers
        scheidsrechter
        thuisploeg {
            naam
            eindplaats
        }
        uitploeg {
            naam
            eindplaats
        }
        score {
            thuisploegDoelpunten
            uitploegDoelpunten
        }
        thuisploegHoekschoppen
        uitploegHoekschoppen
        thuisploegGeleKaarten
        uitploegGeleKaarten
        thuisploegRodeKaarten
        uitploegRodeKaarten
        thuisploegSchoten
        uitploegSchoten
        thuisploegSchotenOpDoel
        uitploegSchotenOpDoel
        thuisploegOvertredingen
        uitploegOvertredingen
        thuisploegBalbezit
        uitploegBalbezit
        thuisploegVerwachteDoelpunten
        uitploegVerwachteDoelpunten
        thuisploegOdds
        gelijkspelOdds
        uitploegOdds
    }
}
```
### Bijlage C
```
{
    speler(name: "{player}") {
        naam
        club {
            naam
        }
        leeftijd
        positie
        minutenGespeeld
        nationaliteit
        aantalDoelpunten
        aantalAssisten
        aantalGeleKaarten
        aantalRodeKaarten
    }
}
```