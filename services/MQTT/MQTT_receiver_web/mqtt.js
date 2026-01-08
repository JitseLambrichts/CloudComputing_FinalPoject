/*
    Deze file beschrijft de MQTT-receiver service. Deze zal subscriben op de MQTT-broker HiveMQ. Via WebSockets zal deze de data "doorsturen" naar de browser.
    Deze dient ook als een Node.js bridge voor gRPC (zie uitleg gRPC-documentatie).
*/

const mqtt = require('mqtt');
const fs = require('fs');
const WebSocket = require('ws');
const grpc = require('@grpc/grpc-js');
const protoLoader = require('@grpc/proto-loader');

const config = JSON.parse(fs.readFileSync('config.json'));

// Nodig voor de data te kunnen koppelen aan de variabelen van de gRPC
const packageDefinition = protoLoader.loadSync('analytics.proto', {
    keepCase: true,
    longs: String,
    enums: String,
    defaults: true,
    oneofs: true
});

// Initialisatie van de gRPC service/servers
const analyticsProto = grpc.loadPackageDefinition(packageDefinition);
const grpcClient = new analyticsProto.AnalyticsService('grpc-server:50051', grpc.credentials.createInsecure());
const clientSteams = new Map();
const lastMQTTData = new Map();

// MQTT-broker variabelen
const brokerAddress = config.brokerAddress;
const brokerPort = config.brokerPort;
const username = config.username;
const password = config.password;
const baseTopic = config.baseTopic;

const client = mqtt.connect({
    host: brokerAddress,
    port: brokerPort,
    username: username,
    password: password,
    protocol: 'mqtts',
    rejectUnauthorized: true
});

// WebSocket initialiseren voor de data te kunnen voorstellen in de browser
const wss = new WebSocket.Server({ port:9292 });

let activeConnections = 0;      // Actieve connecties -> zijn nodig om te controleren als er nog iemand luistert (want als er niemand meer luistert kan deze afgesloten worden)
let messageCount = 0;           // Aantal berichten -> nodig om te unsubscriben na 90 minuten (match-simulatie die 90 minuten duurt)
const MAX_MESSAGES = 90;        // Maximaal aantal berichten -> Om de limiet van de match-simulatie in te stellen (duurt momenteel gewoon 90 minuten)

// Bij het openen van de browser
wss.on('connection', function connection(ws) {
    console.log('Websocket client succesfully connected');

    // Node.js bridge
    const grpcStream = grpcClient.StreamPlayerAnalytics();      // Openen van de bidirectionele stream
    clientSteams.set(ws, grpcStream);                           // Koppelt de gRPC-verbinding aan de specifieke browser-client (ws)

    grpcStream.on('data', (response) => {
        console.log('gRPC Stream repsonse: ', response);

        if (ws.readyState === WebSocket.OPEN) {
            const MQTTData = lastMQTTData.get(ws) || {};

            // Data doorsturen naar de browser
            ws.send(JSON.stringify({
                ...MQTTData,
                type: response.isFinalSummary ? 'summary' : 'analysis',     // Verschil maken tussen een gewone analyse en een summary (summary zal enkel de samenvatting tonen -> zie player.js)
                analysis: {
                    recommendation: response.recommendation,
                    shouldSubstitute: response.shouldSubstitute,
                    fatigueLevel: response.fatigueLevel,
                    avgHeartRate: response.avgHeartRate,
                    avgLactate: response.avgLactate,
                    totalMessages: response.totalMessages
                }
            }));
        }
    });

    grpcStream.on('error', (error) => {
        if (error.details === 'EOF') {                  
            console.log('gRPC Stream closed normally');     // Want bij sluiten zal deze altijd een EOF-error gooien (bronvermelding Copilot)
        } else {
            console.error("gRPC Stream error: ", error);
        }
    })

    grpcStream.on('end', () => {
        console.log('gRPC Stream ended');
    })

    activeConnections++;
    console.log(`Client connected. Active connections: ${activeConnections}`);

    if (activeConnections === 1) {
        client.subscribe(baseTopic, function (err) {
            if (err) {
                console.error('Subscription error: ', err);
            } else {
                console.log('Succesfully subscribed to topic: ', baseTopic);
            }
        });
    }

    ws.on('close', function() {
        // Als er nog een stream verbonden is (gRPC), dan deze stream ook beeïndingen
        const stream = clientSteams.get(ws);
        if (stream) {
            stream.end();
            clientSteams.delete(ws);
        }

        activeConnections--;
        console.log(`Client disconnected. Active connections: ${activeConnections}`);

        // Als er geen actieve luisteraars meer zijn ook unsubscriben van het topic
        if (activeConnections === 0) {
            client.unsubscribe(baseTopic, function(err) {
                if (err) {
                    console.error("Unsubscribe error: ", err);
                } else {
                    console.log("Unsubscribed from topic: ", baseTopic);
                    messageCount = 0;
                }
            });
        }
    });
});


client.on('message', function (mqttTopic, message) {
    if (mqttTopic === baseTopic) {
        messageCount++;

        // Als de limiet bereikt wordt van de simulatie (bij minuut 90 van de match) unsubscriben en de stream stopzetten
        if (messageCount > MAX_MESSAGES) {
            console.log("Match fully simulated, now closing MQTT");
            client.unsubscribe(baseTopic);

            wss.clients.forEach(function each(wsClient) {
                const stream = clientSteams.get(wsClient);
                if (stream) {
                    stream.end();
                }
            })
            return;
        }

        const data = JSON.parse(message.toString());
        console.log('Received message on topic: ', mqttTopic);

        // De data doorsturen naar gRPC voor analyse
        const grpcRequest = {
            currentHeartRate: data.hartslag,
            currentLactate: data.lactaat_waardes,
            timestamp: Date.now()
        };

        wss.clients.forEach(function each(wsClient) {
            if (wsClient.readyState === WebSocket.OPEN) {
                const stream = clientSteams.get(wsClient);
                if (stream) {
                    lastMQTTData.set(wsClient, data);
                    stream.write(grpcRequest);
                }
            }
        });
    }
});

client.on('error', function (error) {
    console.error('Error: ', error)
});


console.log('MQTT-gRPC-WebSocket bridge started');
console.log('WebSocket server listening on port 9292');