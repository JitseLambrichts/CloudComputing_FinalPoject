const urlParams = new URLSearchParams(window.location.search);
const playerName = urlParams.get("player") || "Unknown";

document.getElementById("title").innerText = `Monitoring: ${playerName}`;

const ws = new WebSocket("ws://localhost:9292"); // WebSocket server address

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

// TOEVOEGEN: Stuur spelernaam naar server bij verbinding
ws.onopen = function () {
    console.log("WebSocket connection established");
    // ws.send(JSON.stringify({ type: 'setPlayer', playerName: playerName }));
};

ws.onmessage = function (event) {
    const data = JSON.parse(event.data);

    // Voor front-end printing Copilot --> bronvermelding
    let html = "<h3>Live Prestatie Data</h3>";
    html += `<p><strong>Hartslag:</strong> ${data.hartslag} bpm</p>`;
    html += `<p><strong>Lactaat:</strong> ${data.lactaat_waardes} mmol/L</p>`;
    html += `<p><strong>Systolische Bloeddruk:</strong> ${data.systolische_bloeddruk} mmHg</p>`;
    html += `<p><strong>Zuurstof Opname:</strong> ${data.zuurstof_opname} ml/min</p>`;
    html += `<p><strong>Hartminuutvolume:</strong> ${data.hartminuutvolume} L/min</p>`;
    html += `<p><strong>Maximale Belasting:</strong> ${data.maximale_belasting} W</p>`;
    html += `<p><strong>Anaerobe Drempel:</strong> ${data.anaerobe_drempel} ml/min</p>`;

    if (data.analysis) {
        html += `<hr>`;
        html += "<h3>Analyse (via gRPC)</h3>";
        html += `<p><strong>Aanbeveling:</strong> ${data.analysis.recommendation}</p>`;
        html += `<p><strong>Vermoeidheid:</strong> ${data.analysis.fatigueLevel}/10</p>`;
        html += `<p><strong>Wisselen:</strong> ${
            data.analysis.shouldSubstitute ? "JA ⚠️" : "Nee ✅"
        }</p>`;
    }

    document.getElementById("liveDataText").innerHTML = html;
};

ws.onclose = function () {
    console.log("WebSocket connection closed");
};
