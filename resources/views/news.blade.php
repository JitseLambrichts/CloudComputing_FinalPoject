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
        <div class="container">
            <h1>Latest Sport News</h1>

            <div class="news-grid">
                @forelse($articles as $article)
                    <article class="news-card">
                        <div class="news-image-container">
                            @if(!empty($article['urlToImage']))
                                <img src="{{ $article['urlToImage'] }}" alt="{{ $article['title'] }}" loading="lazy">
                            @else
                                <div class="no-image"><span>📰</span></div>
                            @endif
                        </div>
                        <div class="news-content">
                            <div class="news-meta">
                                <span class="news-source">{{ $article['source']['name'] ?? 'Unknown Source'}}</span>
                            </div>
                            <h2>{{ $article['title'] }}</h2>
                            <p class="news-desc">{{ $article['description'] }}</p>
                            <a href="{{ $article['url'] }}" target="_blank" class="read-more-btn">
                                Read full article
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="no-news">
                        <p>No news available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>