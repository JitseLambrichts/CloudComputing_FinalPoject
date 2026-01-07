const urlParams = new URLSearchParams(window.location.search);
const playerName = urlParams.get("player") || "Unknown";

document.getElementById("title").innerText = `Monitoring: ${playerName}`;

const ws = new WebSocket("ws://127.0.0.1:9292"); // WebSocket server address

async function loadPlayer() {
    try {
        const baseUrl = "http://127.0.0.1:5001";
        const url = `/api/proxy/graphql-player?player=${encodeURIComponent(
            playerName
        )}`;

        const response = await fetch(url);
        const data = await response.json();
        if (data.player) {
            const playerInfoHtml = `
                <div class="stat-item">
                    <span class="stat-label">Nationaliteit</span>
                    <span class="stat-value">${data.player.nationaliteit}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Leeftijd</span>
                    <span class="stat-value">${data.player.leeftijd}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Minuten Gespeeld</span>
                    <span class="stat-value">${data.player.minutenGespeeld}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Positie</span>
                    <span class="stat-value">${data.player.positie}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Club</span>
                    <span class="stat-value">${data.player.club.naam}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Aantal Doelpunten</span>
                    <span class="stat-value">${data.player.aantalDoelpunten}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Aantal Assisten</span>
                    <span class="stat-value">${data.player.aantalAssisten}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Gele Kaarten</span>
                    <span class="stat-value">${data.player.aantalGeleKaarten}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Rode Kaarten</span>
                    <span class="stat-value">${data.player.aantalRodeKaarten}</span>
                </div>
            `;

            document.getElementById("playerStats").innerHTML = playerInfoHtml;
        }
    } catch (error) {
        console.error("Fout bij het laden van speler:", error);
    }
}

loadPlayer();

// Status Indicator Logic
const statusBadge = document.getElementById("connectionStatus");
let dataWatchdog;

function updateStatus(state) {
    if (!statusBadge) return;

    statusBadge.className = "status-badge"; // Reset classes

    switch (state) {
        case "connected":
            statusBadge.innerText = "Live Connection";
            statusBadge.classList.remove("warning", "error");
            break;
        case "disconnected":
            statusBadge.innerText = "Disconnected";
            statusBadge.classList.add("error");
            break;
        case "no-data":
            statusBadge.innerText = "No Data Flow";
            statusBadge.classList.add("warning");
            break;
    }
}

ws.onopen = function () {
    console.log("WebSocket connection established");
    updateStatus("connected");
};

ws.onmessage = function (event) {
    // Reset watchdog on every message
    clearTimeout(dataWatchdog);
    updateStatus("connected");

    // Set watchdog: if no data for 3 seconds, show warning
    dataWatchdog = setTimeout(() => {
        updateStatus("no-data");
    }, 3000);

    const data = JSON.parse(event.data);

    if (data.type === 'summary') {
        let html = "<h3>Match Simulatie Voltooid</h3>";
        html += `<div class="stat-row"><p><strong>Gem. Hartslag:</strong> ${data.analysis.avgHeartRate.toFixed(1)} bpm</p></div>`;
        html += `<div class="stat-row"><p><strong>Gem. Lactaat:</strong> ${data.analysis.avgLactate.toFixed(2)} mmol/L</p></div>`;
        html += `<div class="stat-row"><p><strong>Match:</strong> ${data.analysis.recommendation}</p></div>`;
        
        document.getElementById("liveDataText").innerHTML = html;
        updateStatus("no-data"); // Geen data meer verwacht

        fetch('http://localhost:5001/graphiql', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                query: `mutation {
                    addMinutesPlayed(playerName: "${playerName}", minutesToAdd: ${data.analysis.totalMessages}) {
                        succes
                        message
                        newMinutesTotal
                    }
                }`
            })
        })
        .then(res => res.json())
        .then(result => {
            console.log("Speelminuten bijgewerkt: ", result);
            alert(`${data.analysis.totalMessages} minuten toegeveogd aan ${playerName}`);
        })
        .catch(err => console.error('Fout bij updaten: ', err));
        return;
    }

    // Voor front-end printing Copilot --> bronvermelding
    let html = "<h3>Live Prestatie Data</h3>";
    html += `<div class="stat-row"><p><strong>Hartslag:</strong> ${parseFloat(data.hartslag).toFixed(0)} bpm</p></div>`;
    html += `<div class="stat-row"><p><strong>Lactaat:</strong> ${parseFloat(data.lactaat_waardes).toFixed(1)} mmol/L</p></div>`;
    html += `<div class="stat-row"><p><strong>Systolische Bloeddruk:</strong> ${parseFloat(data.systolische_bloeddruk).toFixed(0)} mmHg</p></div>`;
    html += `<div class="stat-row"><p><strong>Zuurstof Opname:</strong> ${parseFloat(data.zuurstof_opname).toFixed(1)} ml/min</p></div>`;
    html += `<div class="stat-row"><p><strong>Hartminuutvolume:</strong> ${parseFloat(data.hartminuutvolume).toFixed(1)} L/min</p></div>`;
    html += `<div class="stat-row"><p><strong>Maximale Belasting:</strong> ${parseFloat(data.maximale_belasting).toFixed(0)} W</p></div>`;
    html += `<div class="stat-row"><p><strong>Anaerobe Drempel:</strong> ${parseFloat(data.anaerobe_drempel).toFixed(0)} ml/min</p></div>`;
    if (data.analysis) {
        html += "<h3>Analyse (via gRPC)</h3>";
        html += `<div class="stat-row"><p><strong>Aanbeveling:</strong> ${data.analysis.recommendation}</p></div>`;
        html += `<div class="stat-row"><p><strong>Vermoeidheid:</strong> ${data.analysis.fatigueLevel}/10</p></div>`;
        html += `<div class="stat-row"><p><strong>Wisselen:</strong> ${data.analysis.shouldSubstitute ? "JA ⚠️" : "Nee ✅"}</p></div>`;
    }

    document.getElementById("liveDataText").innerHTML = html;
};

ws.onclose = function () {
    console.log("WebSocket connection closed");
    updateStatus("disconnected");
    clearTimeout(dataWatchdog);
};

ws.onerror = function (error) {
    console.error("WebSocket error:", error);
    updateStatus("disconnected");
};
