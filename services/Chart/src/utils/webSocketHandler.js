import { labels, heartRateData, heartBeatChart } from '../charts/heartBeatChart.js';
import { lactateData, lactateChart } from '../charts/lactateChart.js';

const wss = new WebSocket("ws://localhost:9292");

let minute = 1;

wss.onopen = function () {
    console.log("Websocket client connected");
};

wss.onmessage = function (event) {
    try {
        const data = JSON.parse(event.data);

        labels.push(minute);
        minute++;
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