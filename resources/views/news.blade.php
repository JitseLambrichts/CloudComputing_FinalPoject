<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport Nieuws</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .article {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .article img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .article h2 {
            margin-top: 0;
        }
        .article a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>Latest sportnews</h1>

    @forelse($articles as $article)
        <article class="article">
            <h2>{{ $article['title'] }}</h2>
            @if(!empty($article['urlToImage']))
                <img src="{{ $article['urlToImage'] }}" alt="{ $article['title'] }}">
            @endif
            <p>{{ $article['description'] }}</p>
            <p><small>Source: {{ $article['source']['name'] ?? 'Unknown'}}</small></p>
            <a href="{{ $article['url'] }}" target="_blank">Read more</a>
        </article>
    @empty
        <p>No news available</p>
    @endforelse
</body>
</html>