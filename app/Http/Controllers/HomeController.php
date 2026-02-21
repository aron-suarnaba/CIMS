<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function getNewsAPI()
    {
        try {
            $sourceArticles = Cache::remember('tech_news_source', now()->addHours(6), function () {
                $response = Http::withOptions([
                    'verify' => false,
                ])->get('https://saurav.tech/NewsAPI/top-headlines/category/technology/in.json');

                if (! $response->successful()) {
                    return [];
                }

                $data = $response->json();
                return $data['articles'] ?? [];
            });

            $dailyRandomArticle = Cache::remember('tech_news_random_daily_v2', now()->addDay(), function () use ($sourceArticles) {
                $articles = collect($sourceArticles)
                    ->filter(fn ($article) => ! empty($article['title']) && ! empty($article['url']))
                    ->values();

                if ($articles->isEmpty()) {
                    return [];
                }

                return $articles->shuffle()->take(3)->values()->all();
            });

            return response()->json($dailyRandomArticle);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not fetch news: '.$e->getMessage(),
            ], 500);
        }
    }
}
