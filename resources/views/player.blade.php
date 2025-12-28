<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WebSocket MQTT Subscriber</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite(['resources/css/player.css', 'resources/js/player.js'])
</head>
<body>
  <h1>Live Value:</h1>
  <div id="liveValue">
    <h2 id="title">Monitoring: ...</h2>
    <div id="playerStats"></div>
    <hr>
    <div id="liveData">
        <div id="liveDataText"></div>
        <div id="chart-container"> <iframe src="http://localhost:3000"></iframe> </div>
    </div>
  </div>
</body>
</html>
