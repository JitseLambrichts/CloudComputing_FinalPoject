<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WebSocket MQTT Subscriber</title>
</head>
<body>
  <h1>Live Value:</h1>
  <div id="liveValue"></div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const playerName = urlParams.get('player') || 'Unknown';

    document.getElementById('liveValue').innerHTML = `<h2> Monitoring: ${playerName}</h2> <div id="liveData"></div>`;

    const ws = new WebSocket('ws://localhost:9292'); // WebSocket server address

    // TOEVOEGEN: Stuur spelernaam naar server bij verbinding
    ws.onopen = function () {
      console.log('WebSocket connection established');
      ws.send(JSON.stringify({ type: 'setPlayer', playerName: playerName }));
    };

    ws.onmessage = function (event) {
        const data = JSON.parse(event.data);
        
        // Voor front-end printing Copilot --> bronvermelding
        let html = '<h3>Live Prestatie Data</h3>';
        html += `<p><strong>Hartslag:</strong> ${data.hartslag} bpm</p>`;
        html += `<p><strong>Lactaat:</strong> ${data.lactaat_waardes} mmol/L</p>`;
        
        if (data.analysis) {
          html += '<h3>Analyse (via gRPC)</h3>';
          html += `<p><strong>Speler:</strong> ${data.analysis.playerName}</p>`;
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
