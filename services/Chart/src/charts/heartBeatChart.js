// Bronvermelding Chart.js
import Chart from "chart.js/auto";

export const labels = [];
export const heartRateData = [];

// Voor de grafiek te kunnen plotten
const ctx = document.getElementById("heartBeatChart").getContext("2d");
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
