/* 
    Dit is de initialisatie van de grafiek voor de hartslag
    Deze ontvangt de gegevens voor de assen van de Websocket (webSocketHandler.js)

    Bronvermelding Chart.js (https://www.chartjs.org/)
*/ 

import Chart from "chart.js/auto";

// Initialisatie van de assen (worden gevuld in webSocketHandler.js)
export const labels = [];
export const heartRateData = [];

// Voor de grafiek te kunnen plotten in de HTML
const ctx = document.getElementById("heartBeatChart").getContext("2d");

// De echte grafiek initialiseren
const heartBeatChart = new Chart(ctx, {
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
                beginAtZero: true,
            },
        },
    },
});

export { heartBeatChart };
