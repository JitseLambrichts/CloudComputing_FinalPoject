<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- Voor Front-end is gebruik gemaakt van een template --> bronvermelding (zie css) --}}
<body>
    <div class="neon-orbs-container">
        <!-- Top-left orb -->
        <div class="orb orb-top-left">
            <div class="orb-inner orb-light">
                <div class="beam-container beam-spin-8">
                    <div class="beam-light"></div>
                </div>
            </div>
        </div>

        <!-- Bottom-center orb -->
        <div class="orb orb-bottom-center">
            <div class="orb-inner orb-light">
                <div class="beam-container beam-spin-10-reverse">
                    <div class="beam-light"></div>
                </div>
            </div>
        </div>

        <!-- Top-right orb -->
        <div class="orb orb-top-right">
            <div class="orb-inner orb-light">
                <div class="beam-container beam-spin-6">
                    <div class="beam-light"></div>
                </div>
            </div>
        </div>

        <!-- Bottom-right orb -->
        <div class="orb orb-bottom-right">
            <div class="orb-inner orb-light">
                <div class="beam-container beam-spin-7-reverse">
                    <div class="beam-light"></div>
                </div>
            </div>
        </div>

        <!-- Center content -->
        <div class="center-content">
            <h1 class="title">VOETBAL MANAGER</h1>
            <p class="subtitle">DE CENTRALE PLAATS VOOR AL JOUW VOETBALGEGEVENS</p>
            
            <div class="links-container">
                <a href="{{ route('grpc') }}">Ga naar GRPC</a>
                <a href="{{ route('matches') }}">Ga naar Matches</a>
                <a href="{{ route('data') }}">Ga naar Data</a>
            </div>
        </div>
    </div>
</body>
</html>