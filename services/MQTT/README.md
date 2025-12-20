# Taak 4: MQTT

Deze taak beschrijft de prestaties van een voetbalmatch via MQTT. Deze genereert dan random data (via de MQTT_sender) en consumeert deze dan (MQTT_receiver). Als uitbreiding heb ik ook een grafiek toegevoegd. Eerst via matplotlib, maar aangezien dit niet compatibel is met Docker zal deze werken met een afbeelding (MQTT_receiver/plots/live_grafiek.png). Dit is stelt dan de live grafiek voor.

# Service samen opstarten
```docker compose up --build```

# Services apart opstarten
## Receiver opstarten
```cd MQTT_receiver && docker compose up --build```

## Sender opstarten 
```cd MQTT_sender && docker run --rm mqtt-simulator```