# Project Cloud Computing: Football manager

This project simulates a football manager. You can search for teams, see player performances (simulated) and read the latest football news.

## Services
- SOAP: retrieves data from database for GraphQL
- GraphQL: query the matches, team- and player-statistics
- MQTT: simulate the performance of a player
- gRPC: analyzes this performance
- WebSockets: used to easily access MQTT and gRPC data

## The different views:
### Team view
In this view, you can search for a Premier League team. This will return the statistics of this team for the 2018-2019 season.
The team-statistics are retrieved from the GraphQL services that uses SOAP to get the data from the database. The matches are retrieved directly from the database to avoid latency. If you expand the player-widget, you can see all the players for this team. Here, you can navigate to the player view.

### Player view
In this view, you can see the player-statistics that are also retrieved from the database using the GraphQL-SOAP pipeline. The player-data are used to simulate the performance of players during a match. This is done by MQTT. The player-analytics are based on this data from the MQTT. This data is analyzed by gRPC. The charts are built by the library of chart.js. The data from MQTT and gRPC is published on a WebSocket so the data flow is easily accessible.

### News view
This view shows the latest sport news from the Premier League (if news is available). This uses a third-party API called NewsAPI.

## Architecture
![Architectuurdiagram](./storage/app/public/diagram.png)