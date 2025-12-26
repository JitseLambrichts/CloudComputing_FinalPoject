// Bronvermelding Chart.js
import Chart from "chart.js/auto";

let minute = 1;
const labels = [];
const heartRateData = [];

// Voor de grafiek te kunnen plotten
const ctx = document.getElementById("myChart").getContext("2d");
const myChart = new Chart(ctx, {
    type: "line",
    data: {
        labels: labels,
        datasets: [
            {
                label: "Hartslag",
                data: heartRateData,
                borderColor: "rgb(255, 0, 0)",
                tension: 0.1,
            },
        ],
    },
    /* Pas dit aan in services/Chart/src/chart.js */
    options: {
        responsive: true,
        scales: {
            x: {
                title: {
                    display: true,
                    text: "Minuut"
                }
            },
            y: {
                title: {
                    display: true,
                    text: "Hartslag (bpm)"
                },
                beginAtZero: true, // Deze moet buiten 'title' staan
            },
        },
    },
});

const wss = new WebSocket("ws://localhost:9292");

wss.onopen = function () {
    console.log("Websocket client connected");
};

wss.onmessage = function (event) {
    try {
        const data = JSON.parse(event.data);

        labels.push(minute);
        minute++;
        heartRateData.push(data.hartslag);

        if (labels.length > 90) {
            labels.shift();
            heartRateData.shift();
        }

        myChart.update();
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
