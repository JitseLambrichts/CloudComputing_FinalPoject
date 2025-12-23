<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WebSocket MQTT Subscriber</title>
</head>
<body>
  <h1>Live Value:</h1>
  <div id="liveValue">
    <h2 id="title">Monitoring: ...</h2>
    <div id="playerStats"></div>
    <hr>
    <div id="liveData"></div>
  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const playerName = urlParams.get('player') || 'Unknown';

    document.getElementById('title').innerText = `Monitoring: ${playerName}`;

    const ws = new WebSocket('ws://localhost:9292'); // WebSocket server address

    async function loadPlayer() {
        try {
            const baseUrl  = "http://127.0.0.1:5001";
            const url = `/api/proxy/graphql-player?player=${encodeURIComponent(playerName)}`

            const response = await fetch(url);
            const data = await response.json();
            if (data.player) {
                const playerInfoHtml = `
                    <p>Nationaliteit: ${data.player.nationaliteit}</p>
                    <p>Leeftijd: ${data.player.leeftijd}</p>
                    <p>Aantal minuten gespeeld: ${data.player.minutenGespeeld}</p>
                `

                document.getElementById('playerStats').innerHTML = playerInfoHtml;
            }
        } catch (error) {
            console.error("Fout bij het laden van speler:", error);
        }
    }

    loadPlayer();

    // TOEVOEGEN: Stuur spelernaam naar server bij verbinding
    ws.onopen = function () {
      console.log('WebSocket connection established');
      // ws.send(JSON.stringify({ type: 'setPlayer', playerName: playerName }));
    };

    ws.onmessage = function (event) {
        const data = JSON.parse(event.data);
        
        // Voor front-end printing Copilot --> bronvermelding
        let html = '<h3>Live Prestatie Data</h3>';
        html += `<p><strong>Hartslag:</strong> ${data.hartslag} bpm</p>`;
        html += `<p><strong>Lactaat:</strong> ${data.lactaat_waardes} mmol/L</p>`;
        
        if (data.analysis) {
          html += '<h3>Analyse (via gRPC)</h3>';
          html += `<p><strong>Aanbeveling:</strong> ${data.analysis.recommendation}</p>`;
          html += `<p><strong>Vermoeidheid:</strong> ${data.analysis.fatigueLevel}/10</p>`;
          html += `<p><strong>Wisselen:</strong> ${data.analysis.shouldSubstitute ? 'JA ⚠️' : 'Nee ✅'}</p>`;
        }
        
        document.getElementById('liveData').innerHTML = html;
    };

    ws.onclose = function () {
      console.log('WebSocket connection closed');
    };
</script>
</body>
</html>
