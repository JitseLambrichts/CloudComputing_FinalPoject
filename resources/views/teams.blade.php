<!--Voor front-end (styling) => bronvermelding Copilot -->

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voetbal Wedstrijden</title>
    @vite(['resources/css/teams.css', 'resources/css/navbar.css'])
</head>
<body>
    @include('partials.navbar')
    
    <div class="main-content-with-sidebar">
        <div class="container">
            <h1>Premier League Matches</h1>
            <form>
                <input type="text" id="team" placeholder="Zoek een Premier League team...">
                <button type="button" onclick="loadMatches()">Zoek</button>
            </form>
            <br>
            <br>
            <div id="teaminfocontainer"></div>                              <!-- Container voor de team-informatie -->
            <div id="matches-container" class="matches-grid"></div>         <!-- Container voor de matches-containers -->
            <br>
            <br>
        </div>
    </div>

    <script>
        async function loadMatches() {
            const teamName = document.getElementById("team").value;

            try {
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
                                            <a href="{{ route('player') }}?player=${encodeURIComponent(player.naam)}">${player.naam}</a>
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
                } else {
                    document.getElementById('matches-container').innerHTML = 
                        '<div class="error">Team niet gevonden.</div>';
                    return;
                }

                // Toon de wedstrijden
                const matchesHtml = data.matches.map(match => {
                    // Datum omzetten naar een datum object
                    const datumObj = new Date(match.datum);
                    
                    // Als niet kan worden omgeze, dan omzetten naar een string (hulp Copilot -> bronvermelding)
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
                        <div class="match-details-container">
                            <div class="match-info"><strong>Stadion: </strong>${match.stadion}</div>
                            <div class="match-info"><strong>Aantal bezoekers: </strong>${match.aantalBezoekers}</div>
                            <div class="match-info"><strong>Scheidsrechter: </strong>${match.scheidsrechter}</div>
                        </div>
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
                        <br>
                        <button class="more-info-toggle" onclick="toggleMatchInfo(this)">▼ Meer info</button>
                        <div class="match-extra-info">
                            <h4>📊 Extra Statistieken</h4>
                            <p><strong>🟨 Gele kaarten:</strong> ${match.thuisploegGeleKaarten || 0} - ${match.uitploegGeleKaarten || 0}</p>
                            <p><strong>🟥 Rode kaarten:</strong> ${match.thuisploegRodeKaarten || 0} - ${match.uitploegRodeKaarten || 0}</p>
                            <p><strong>⚽ Schoten:</strong> ${match.thuisploegSchoten || 0} - ${match.uitploegSchoten || 0}</p>
                            <p><strong>🎯 Schoten op doel:</strong> ${match.thuisploegSchotenOpDoel || 0} - ${match.uitploegSchotenOpDoel || 0}</p>
                            <p><strong>Verwachte doelpunten:</strong> ${match.thuisploegVerwachteDoelpunten || 0} - ${match.uitploegVerwachteDoelpunten || 0}</p>
                            <p><strong>🚩 Hoekschoppen:</strong> ${match.thuisploegHoekschoppen || 0} - ${match.uitploegHoekschoppen || 0}</p>
                            <p><strong>⚠️ Overtredingen:</strong> ${match.thuisploegOvertredingen || 0} - ${match.uitploegOvertredingen || 0}</p>
                            <p><strong>📈 Balbezit:</strong> ${match.thuisploegBalbezit || 0}% - ${match.uitploegBalbezit || 0}%</p>
                            <p><strong>Odds:</strong> ${match.thuisploegOdds || 0} - ${match.gelijkspelOdds || 0} - ${match.uitploegOdds || 0}</p>
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

        function toggleMatchInfo(button) {
            const extraInfo = button.nextElementSibling;
            extraInfo.classList.toggle('show');
            button.textContent = extraInfo.classList.contains('show') ? '▲ Minder info' : '▼ Meer info';
        }

    </script>
</body>
</html>