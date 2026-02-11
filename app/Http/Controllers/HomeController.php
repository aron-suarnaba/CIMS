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
        // We wrap the logic inside Cache::remember
        // 'tech_news' is the key, 86400 is 24 hours (use 3600 for 1 hour)
        $articles = Cache::remember('tech_news', 86400, function () {

            // $response = Http::withOptions([
            //     'verify' => false, // Fixes local SSL issues
            // ])->get("https://newsapi.org/v2/top-headlines", [
            //     'country'  => 'us',
            //     'category' => 'technology',
            //     'apiKey'   => '6249d6d9b05f4c66b29d67a0ff4f46da',
            // ]);
            $response = Http::withOptions([
                'verify' => false,
            ])->get("https://saurav.tech/NewsAPI/top-headlines/category/technology/in.json");

            if ($response->successful()) {
                $data = $response->json();
                return $data['articles'] ?? [];
            }
            return [];
        });

        // Return the cached (or freshly fetched) articles
        return response()->json($articles);

    } catch (\Exception $e) {
        return response()->json(["error" => "Could not fetch news: " . $e->getMessage()], 500);
    }
}
    // public function getNewsAPI()
    // {
    //     try {
    //         // 1. Bypass cache for testing
    //         // 2. Add 'verify' => false to handle local SSL issues
    //         $response = Http::withOptions([
    //             'verify' => false,
    //         ])->get("https://newsapi.org/v2/top-headlines", [
    //                     'country' => 'us',
    //                     'category' => 'technology',
    //                     'apiKey' => '6249d6d9b05f4c66b29d67a0ff4f46da', // Hardcoded for 1 minute to test
    //                 ]);

    //         if ($response->successful()) {
    //             $data = $response->json();
    //             // NewsAPI returns an object with an 'articles' key
    //             return response()->json($data['articles'] ?? []);
    //         }

    //         // If it fails, return the error from the API so we can see it
    //         return response()->json([
    //             'error' => 'API Error',
    //             'status' => $response->status(),
    //             'body' => $response->body()
    //         ], $response->status());

    //     } catch (\Exception $e) {
    //         return response()->json(["error" => $e->getMessage()], 500);
    //     }
    // }

}
