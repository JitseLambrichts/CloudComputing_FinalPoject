<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport Nieuws</title>
    @vite(['resources/css/news.css', 'resources/css/navbar.css'])
</head>
<body>
    @include('partials.navbar')
    
    <div class="main-content-with-sidebar">
        <h1>Latest sportnews</h1>

        @forelse($articles as $article)
            <article class="article">
                <h2>{{ $article['title'] }}</h2>
                @if(!empty($article['urlToImage']))
                    <img src="{{ $article['urlToImage'] }}" alt="{{ $article['title'] }}">
                @endif
                <p>{{ $article['description'] }}</p>
                <p><small>Source: {{ $article['source']['name'] ?? 'Unknown'}}</small></p>
                <a href="{{ $article['url'] }}" target="_blank">Read more</a>
            </article>
        @empty
            <p>No news available</p>
        @endforelse
    </div>
</body>
</html>