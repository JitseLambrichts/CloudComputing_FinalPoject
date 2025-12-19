Standaard queries aangemaakt met Copilot, zelf verder aangevuld

## Een speler opzoeken
```
query {
  speler(name: "Mohamed Salah") {
    naam
    leeftijd
    geboorteDatum
    positie
    nationaliteit
    minutenGespeeld
    aantalDoelpunten
    aantalAssisten
    aantalGeleKaarten
    aantalRodeKaarten
    club {
      naam
      eindplaats
    }
  }
}
```

## Competitie gegevens ophalen
```
{
  league(id: 0) {
    naam
    seizoen
    aantalMatches
    gemDoelpunten
    matches(limit: 5) {
      thuisploeg {
        naam
      }
      uitploeg {
        naam
      }
      score {
        thuisploegDoelpunten
        uitploegDoelpunten
      }
    }
  }
}
```

## Alle wedstrijgden ophalen (met paginatie)
```
query {
  matches(limit: 5, offset: 0) {
    datum
    stadion
    aantalBezoekers
    thuisploeg {
      naam
    }
    uitploeg {
      naam
    }
    score {
      thuisploegDoelpunten
      uitploegDoelpunten
    }
    winnaar {
      naam
    }
  }
}
```

## Wedstrijden van een specifiek team
```
query {
  teamMatches(teamName: "Liverpool", limit: 10) {
    datum
    thuisploeg {
      naam
    }
    uitploeg {
      naam
    }
    score {
      thuisploegDoelpunten
      thuisploegTijdstippenDoelpunten
      uitploegDoelpunten
      uitploegTijdstippenDoelpunten
      thuisploegVerwachteDoelpunten
      uitploegVerwachteDoelpunten
    }
  }
}
```

## Wedstrijd details
```
query {
  matches(limit: 1) {
    datum
    stadion
    aantalBezoekers
    scheidsrechter
    thuisploeg {
      naam
      wedstrijdenGewonnenThuis
      wedstrijdenGespeeld
      wedstrijdenVerlorenThuis
      gemPuntenPerMatch
      eindplaats
      doelpuntenGemaakt
      doelpuntenTegen
    }
    uitploeg {
      naam
      wedstrijdenGewonnenUit
      wedstrijdenGespeeld
      wedstrijdenVerlorenUit
      gemPuntenPerMatch
      eindplaats
      doelpuntenGemaakt
      doelpuntenTegen
    }
    score {
      thuisploegDoelpunten
      uitploegDoelpunten
      thuisploegTijdstippenDoelpunten
      uitploegTijdstippenDoelpunten
    }
  }
}
``