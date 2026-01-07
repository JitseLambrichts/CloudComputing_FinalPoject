/* 
    Dit is de initialisatie van de grafiek voor de lactaat-waardes
    Deze ontvangt de gegevens voor de assen van de Websocket (webSocketHandler.js)

    Bronvermelding Chart.js (https://www.chartjs.org/)
*/ 

import Chart from "chart.js/auto";
import { labels } from './heartBeatChart.js';

// Initialisatie van de as (worden gevuld in webSocketHandler.js)
// De x-as wordt geïmporteerd vanuit de hartslag grafiek (om duplicate code te vermijden)
export const lactateData = [];

// Voor de grafiek te kunnen plotten in de HTML
const ctx = document.getElementById("lactateChart").getContext("2d");

// De echte grafiek initialiseren
const lactateChart = new Chart(ctx, {
    type: "line",
    data: {
        labels: labels,
        datasets: [
            {
                label: "Lactate",
                data: lactateData,
                borderColor: "rgb(0, 255, 0)",
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
                    text: "Lactaat-waardes (mmHg)"
                },
                beginAtZero: true,
            },
        },
    },
});

export { lactateChart };