<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Live Prestatie Data</title>
</head>
<body>
    <img id="liveGrafiek" src="/mqtt/live_grafiek.png" alt="Live Prestatie Grafiek">

    <script>
        // Ververs de afbeelding elke seconde
        setInterval(function() {
            const img = document.getElementById('liveGrafiek');
            // Voeg timestamp toe om browser cache te omzeilen
            img.src = '/mqtt/live_grafiek.png?' + new Date().getTime();
        }, 1000); // 1000ms = 1 seconde
    </script>
</body>
</html>