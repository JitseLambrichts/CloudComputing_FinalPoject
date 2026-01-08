# Service - gRPC (Go)

Deze service stelt de analyse van spelerprestaties voor. Deze ontvangt de data van de MQTT service, namelijk de hartslag (CurrentHeartRate), lactaatwaardes (currentLactate) als ook het tijdstip (timestamp). Deze verkrijgt de data via een Node.js bridge (mqtt.js). Deze bridge is noodzakelijk aangezien JSON (MQTT) niet gelijk is aan Protobuf (gRCP). Dit houdt in dat de MQTT Broker niet direct een gRPC server kan opzetten, maar dat gRPC zich ook niet direct kan abonneren op MQTT.

Dit is een bi-directionele stream wat wil zeggen dat deze meerdere requests als ook meerdere responses verwerkt. In dit project is geïmplementeerd door de gemiddelde waardes bij te houden. Dit is mogelijk doordat bij een bi-directionele ook 'toegang' heeft tot de vorige messages, dus deze kan zijn status onthouden. ZO kan deze alle hartslag-waardes opslaan en dan de totale som delen door het aantal messages.

De MQTT-service simuleert 90 datapunten die dan de 90 minuten van een match moeten voorstellen, en als deze 90 punten ontvangen zijn, dan unsubcribed de MQTT-receiver. Op dit moment wordt er een error opgevangen, en dan weet de gRCP dat dit het finale bericht is. (zie StreamPlayerAnalytics.go)

Endpoint: `http://localhost:50051`