<!--Voor front-end (styling) => bronvermelding Copilot -->

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voetbal Wedstrijden</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #76FBCE 0%, #7C39EC 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        h1 {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .matches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .match-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .match-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }
        
        .match-date {
            color: #667eea;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .match-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        
        .team {
            flex: 1;
            text-align: center;
        }
        
        .team-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        .vs {
            font-size: 12px;
            color: #999;
            font-weight: bold;
            padding: 0 10px;
        }
        
        .loading {
            text-align: center;
            color: white;
            font-size: 18px;
        }
        
        .error {
            background: #ff6b6b;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        h2 {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        h3 {
            text-decoration: underline;
        }
        
        form {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        input[type="text"] {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        button:hover {
            background: #764ba2;
        }

        .dropdown-toggle {
            background: none;
            border: none;
            color: #667eea;
            font-weight: bold;
            cursor: pointer;
            padding: 0;
            font-size: 14px;
            margin-left: 10px;
        }

        .dropdown-toggle:hover {
            background: none;
            color: #764ba2;
        }

        .players-list {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            display: none;
        }

        .players-list.show {
            display: block;
        }

        .player-item {
            padding: 8px 0;
            color: #555;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚽ Premier League Matches via GraphQL</h1>
        <form>
            <input type="text" id="team" placeholder="Geef hier een ploegnaam in om te zoeken (uit de Premier League)...">
            <button type="button" onclick="loadMatches()">Zoek</button>
        </form>
        <br>
        <br>
        <div id="teaminfocontainer"></div>      <!-- Container voor de team-informatie -->
        <div id="matches-container" class="matches-grid"></div>   <!-- Container voor de matches-containers -->
        <br>
        <br>
    </div>

    <script>
        async function loadMatches() {
            const teamName = document.getElementById("team").value;

            try {
                // Zorg dat deze poort (5001) overeenkomt met je docker-compose configuratie
                const baseUrl = "http://127.0.0.1:5001"; 
                const url = teamName ? `/api/proxy/graphql-matches?team=${encodeURIComponent(teamName)}` : `/api/proxy/graphql-matches`;

                const response = await fetch(url);
                const data = await response.json();

                // Toon team statistieken
                // Voor dropdown-menu hulp van Copilot (bronvermelding)
                if (data.team) {
                    const teamInfoHtml = `
                        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h3>${data.team.naam} <button class="dropdown-toggle" onclick="togglePlayers()">▼ Spelers</button></h3>
                            <br>
                            <p><strong>Eindplaats:</strong> ${data.team.eindplaats || 'N/A'}</p>
                            <p><strong>Gespeeld:</strong> ${data.team.wedstrijdenGespeeld || 'N/A'}</p>
                            <p><strong>Gewonnen:</strong> ${data.team.wedstrijdenGewonnen || 'N/A'}</p>
                            <p><strong>Gewonnen Thuis:</strong> ${data.team.wedstrijdenGewonnenThuis || 'N/A'}</p>
                            <p><strong>Verloren:</strong> ${data.team.wedstrijdenVerloren || 'N/A'}</p>
                            <p><strong>Verloren Thuis:</strong> ${data.team.wedstrijdenVerlorenThuis || 'N/A'}</p>
                            <p><strong>Gelijkspel:</strong> ${data.team.wedstrijdenGelijkspel || 'N/A'}</p>
                            <p><strong>Gemiddelde punten per match:</strong> ${data.team.gemPuntenPerMatch || 'N/A'}</p>
                            <p><strong>Doelpunten voor:</strong> ${data.team.doelpuntenGemaakt || 'N/A'}</p>
                            <p><strong>Doelpunten tegen:</strong> ${data.team.doelpuntenTegen || 'N/A'}</p>
                            <div id="players-container" class="players-list">
                                ${data.team.spelers && data.team.spelers.length > 0 
                                    ? data.team.spelers.map(player => `
                                        <div class="player-item">
                                            • <a href="{{ route('live-data') }}">${player.naam}</a> - Leeftijd: ${player.leeftijd} - Positie: ${player.positie} - Minuten gespeeld: ${player.minutenGespeeld}
                                        </div>`).join('')
                                    : '<div class="player-item">Geen spelers beschikbaar</div>'
                                }
                            </div>
                        </div>
                        <br>
                        <h2>Alle matches van ${data.team.naam}:</h2>
                        <br>
                    `;

                    document.getElementById('teaminfocontainer').innerHTML = teamInfoHtml;
                }

                // Toon de wedstrijden
                const matchesHtml = data.matches.map(match => {
                    // Datum omzetten naar een datum object
                    const datumObj = new Date(match.datum);
                    
                    // Als niet kan worden omgeze, dan omzetten naar een string (hulp copilot -> bronvermelding)
                    const datumNL = isNaN(datumObj.getTime()) 
                        ? match.datum 
                        : datumObj.toLocaleDateString('nl-NL', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                                
                    // De HTML code returnen die op de pagina zal worden weergegeven voor de matches
                    return `
                    <div class="match-card">
                        <div class="match-date">${datumNL}</div>
                        <div class="match-info"><strong>Stadion: </strong>${match.stadion}</div>
                        <div class="match-info"><strong>Aantal bezoekers: </strong>${match.aantalBezoekers}</div>
                        <div class="match-info"><strong>Scheidsrechter: </strong>${match.scheidsrechter}</div>
                        <br>
                        <br>
                        <div class="match-content">
                            <div class="team">
                                <div class="team-name">${match.thuisploeg.naam}</div>
                                <div class="score">${match.score.thuisploegDoelpunten}</div>
                            </div>
                            <div class="vs">VS</div>
                            <div class="team">
                                <div class="team-name">${match.uitploeg.naam}</div>
                                <div class="score">${match.score.uitploegDoelpunten}</div>
                            </div>
                        </div>
                    </div>
                `;
                }).join('');
                
                document.getElementById('matches-container').innerHTML = matchesHtml || 
                    '<div class="error">Geen wedstrijden gevonden</div>';
            } catch (error) {
                document.getElementById('matches-container').innerHTML = 
                    `<div class="error">Fout bij laden: ${error.message}</div>`;
            }
        }

        // Om de spelers te kunnen weergeven
        function togglePlayers() {
            const container = document.getElementById('players-container');
            container.classList.toggle('show');
        }
    </script>
</body>
</html>