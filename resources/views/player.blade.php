<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WebSocket MQTT Subscriber</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite(['resources/css/player.css', 'resources/js/player.js', 'resources/css/navbar.css'])
</head>
@include('partials.navbar')
  
<div class="main-content-with-sidebar">
    <div class="container">
        <div class="dashboard-header">
            <h1 id="title">Monitoring Player</h1>
            <div class="status-badge">Live Connection</div>
        </div>

        <div class="dashboard-grid">
            <!-- Player Stats Card -->
            <div class="card stats-card">
                <div class="card-header">
                    <h2>Player Statistics</h2>
                </div>
                <div class="card-body">
                    <div id="playerStats" class="stats-content">Loading stats...</div>
                </div>
            </div>

            <!-- Live Data Card -->
            <div class="card live-card">
                <div class="card-header">
                    <h2>Live Performance Data</h2>
                </div>
                <div class="card-body live-data-layout">
                    <div id="liveDataText" class="live-text-panel">
                        <!-- Live text data will be injected here -->
                    </div>
                    <div id="chart-container" class="chart-panel">
                        <iframe src="http://localhost:3000" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</html>
