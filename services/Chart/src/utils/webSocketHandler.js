/*
    Dit is de websocket verbinding om de data van MQTT te kunnen visualiseren in de grafieken.
    Deze luistert naar de inkomende berichten van de MQTT-gRPC bridge en vertaalt deze naar updates voor de Chart.js grafieken
*/

import { labels, heartRateData, heartBeatChart } from '../charts/heartBeatChart.js';
import { lactateData, lactateChart } from '../charts/lactateChart.js';

const wss = new WebSocket("ws://127.0.0.1:9292");

let minute = 1;

wss.onopen = function () {
    console.log("Websocket client connected");
};

// Verwacht formaat: { "hartslag": double, "lactaat_waardes": double } (komt van de MQTT sender)
wss.onmessage = function (event) {
    try {
        const data = JSON.parse(event.data);

        // Minuten toevoegen aan de x-as
        labels.push(minute);
        minute++;

        // De echte data opvangen voor de verschillende y-assen
        heartRateData.push(data.hartslag);
        lactateData.push(data.lactaat_waardes);

        heartBeatChart.update();
        lactateChart.update();
    } catch (e) {
        console.log("Received non-JSON message: ", event.data);
    }
};

wss.onerror = function (error) {
    console.log("Error: ", error);
};

wss.onclose = function () {
    console.log("Websocket client disconnected");
};