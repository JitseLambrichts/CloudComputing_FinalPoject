// Bronvermelding Chart.js
import Chart from "chart.js/auto";
import { labels } from './heartBeatChart.js';

export const lactateData = [];

// Voor de grafiek te kunnen plotten
const ctx = document.getElementById("lactateChart").getContext("2d");
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