const mqtt = require('mqtt');
const fs = require('fs');
const WebSocket = require('ws');

const config = JSON.parse(fs.readFileSync('config.json'));

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
            const data = message.toString();
            console.log('Received message on topic: ', mqttTopic);
            ws.send(data);
        }
    });

    client.on('error', function (error) {
        console.error('Error: ', error)
    });

    ws.on('close', function() {
        console.log('Websocket client disconnected');
    });
});