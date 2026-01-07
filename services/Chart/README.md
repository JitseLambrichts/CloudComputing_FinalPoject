# Services - Chart

Dit is de service die ervoor zorgt dat de grafiek in "real-time" kunnen geplot worden. Hier wordt gebruik gemaakt van de library van Chart.js (bronvermelding: https://www.chartjs.org/).

Deze service verbind met dezelfde WebSocket waarop de MQTT en gRPC hun data posten. Hierdoor kunnen de grafieken opgesteld worden op basis van de data die ontvangen wordt van de MQTT-service.

In deze implementatie wordt de data van de hartslag en de lactaat-waardes geplot.
(Voor specifiekere documentatie, zie documentatie bij bestanden)