<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    // public function getNewsAPI()
    // {

    //     try {
    //         $articles = Cache::remember('tech_news', 3600, function () {
    //             $response = Http::get("https://newsapi.org/v2/top-headlines", [
    //                 'country' => 'us',
    //                 'category' => 'technology',
    //                 'apikey' => config('services.news_api_key'),
    //             ]);
    //             if ($response->successful()) {
    //                 return $response->json()['articles'] ?? [];
    //             }
    //             return [];

    //         });
    //         return response()->json($articles);
    //     } catch (\Exception $e) {
    //         return response()->json(["error" => "Could not fetch news: " . $e->getMessage()], 500);
    //     }

    // }
    public function getNewsAPI()
    {
        try {
            // 1. Bypass cache for testing
            // 2. Add 'verify' => false to handle local SSL issues
            $response = Http::withOptions([
                'verify' => false,
            ])->get("https://newsapi.org/v2/top-headlines", [
                        'country' => 'us',
                        'category' => 'technology',
                        'apiKey' => '6249d6d9b05f4c66b29d67a0ff4f46da', // Hardcoded for 1 minute to test
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                // NewsAPI returns an object with an 'articles' key
                return response()->json($data['articles'] ?? []);
            }

            // If it fails, return the error from the API so we can see it
            return response()->json([
                'error' => 'API Error',
                'status' => $response->status(),
                'body' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

}
