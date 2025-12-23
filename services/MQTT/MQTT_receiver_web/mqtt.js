const mqtt = require('mqtt');
const fs = require('fs');
const WebSocket = require('ws');
const grpc = require('@grpc/grpc-js');
const protoLoader = require('@grpc/proto-loader');

const config = JSON.parse(fs.readFileSync('config.json'));

const packageDefinition = protoLoader.loadSync('analytics.proto', {
    keepCase: true,
    longs: String,
    enums: String,
    defaults: true,
    oneofs: true
});

const analysticsProto = grpc.loadPackageDefinition(packageDefinition);
const grpcClient = new analysticsProto.AnalyticsService('grpc-server:50051', grpc.credentials.createInsecure());

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

const wss = new WebSocket.Server({ port:9292 });

wss.on('connection', function connection(ws) {
    console.log('Websocket client succesfully connected');

    let currentPlayerName = 'Unknown';

    // Luister naar berichten van de client (spelernaam)
    ws.on('message', function incoming(message) {
        try {
            const clientMessage = JSON.parse(message);
            if (clientMessage.type === 'setPlayer') {
                currentPlayerName = clientMessage.playerName;
                console.log('Player name set to:', currentPlayerName);
            }
        } catch (e) {
            console.log('Received non-JSON message:', message);
        }
    });

    const topic = baseTopic;
    client.subscribe(topic, function (err) {
        if (err) {
            console.error('Subscription error: ', err);
        } else {
            console.log('Succesfully subscribed to topic: ', topic);
        }
    });

    client.on('message', function (mqttTopic, message) {
        if (mqttTopic === topic) {
            const data = JSON.parse(message.toString());
            console.log('Received message on topic: ', mqttTopic);

            const grpcRequest = {
                currentHeartRate: data.hartslag,
                currentLactate: data.lactaat_waardes,
                timestamp: Date.now()
            };

            grpcClient.AnalyzePlayer(grpcRequest, (error, response) => {
                if (error) {
                    console.error("gRPC error: ", error);
                    ws.send(JSON.stringify(data));
                } else {
                    console.log("gRPC response: ", response);
                    const enrichedData = {
                        ...data,
                        analysis: {
                            recommendation: response.recommendation,
                            shouldSubstitute: response.shouldSubstitute,
                            fatigueLevel: response.fatigueLevel
                        }
                    };
                    ws.send(JSON.stringify(enrichedData));
                } 
            });
        }
    });

    client.on('error', function (error) {
        console.error('Error: ', error)
    });

    ws.on('close', function() {
        console.log('Websocket client disconnected');
    });
});


console.log('MQTT-gRPC-WebSocket bridge started');
console.log('WebSocket server listening on port 9292');