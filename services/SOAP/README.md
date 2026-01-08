# Service - SOAP (Java)

Deze service zal de data uit de database gaan halen (voor GraphQL). Deze haalt de statistieken voor zowel de teams als de spelers op. Verder zal deze ook nog ervoor zorgen dat de spelersminuten worden geupdate als de MQTT simulatie eindigd (zie bijlage A).
Deze maakt gebruik van een soort service contract, dit is de team.xsd (src/main/java/resources). Op basis van deze XSD zullen verschillende standaard-klasses worden aangemaakt.
Om deze service ook beschikbaar te stellen naar andere services, zoals GraphQL, maakt deze gebruik van SpringBoot (WebServiceConfig.java). 

Om ook een klein beetje aan veiligheid te denken worden de username en het password van database ook uit de .env uitgelezen.

De service verbindt met de database, voert dan de nodige query uit (afhankelijk van wat de gebruiker 'vraagt'). De response zal dan doorgestuurd worden naar GraphQL.

Endpoint: `http://localhost:8001/ws`  (en WSDL contract: `http://localhost:8001/ws/football.wsdl`)

# Bijlage
### Bijlage A
- Teamstatistieken: getTeamStatsRequest
- Spelerstatistieken: getPlayerStatsRequest
- Speelminuten updaten: updatePlayerMinutesRequest

### Bijlage B
Hoe ziet zo'n request eruit:
```xml
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <getTeamStatsRequest xmlns="http://be/cloud/team_statistics">
      <teamName>Liverpool</teamName>
    </getTeamStatsRequest>
  </soap:Body>
</soap:Envelope>
```