<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Preference;
use App\Transformers\ArticleTransformer;
use Illuminate\Support\Facades\Auth;

/**
 * Class FeedController
 *
 * This controller handles the provision of articles as per user preferences.
 */
class FeedController extends Controller
{
    /**
     * Fetch a personalized news feed based on user preferences.
     *
     * This method retrieves the authenticated user's preferences, including categories,
     * sources, and authors, and uses them to filter articles accordingly. The filtered
     * articles are then returned in the response.
     *
     * @OA\Get(
     *     path="/api/personalized-feed",
     *     summary="Get personalized news feed",
     *     description="Fetch a personalized news feed based on user preferences, including categories, sources, and authors.",
     *     operationId="getPersonalizedFeed",
     *     tags={"Personalized Feed"},
     *
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         description="Bearer token for authentication",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with personalized articles",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/Article")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function personalizedFeed()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Fetch user preferences
        $preferences = Preference::where('user_id', $user->id)->get();

        // Extract preference values (e.g., categories, authors, sources)
        $categories = $preferences->where('name', 'category')->pluck('value')->toArray();
        $sources = $preferences->where('name', 'source')->pluck('value')->toArray();
        $authors = $preferences->where('name', 'author')->pluck('value')->toArray();

        // Query articles based on preferences
        $articlesQuery = Article::query();

        if (! empty($categories)) {
            $articlesQuery->whereHas('attributes', function ($query) use ($categories) {
                $query->where('name', 'category')->whereIn('value', $categories);
            });
        }

        if (! empty($sources)) {
            $articlesQuery->whereHas('attributes', function ($query) use ($sources) {
                $query->where('name', 'source')->whereIn('value', $sources);
            });
        }

        if (! empty($authors)) {
            $articlesQuery->whereHas('attributes', function ($query) use ($authors) {
                $query->where('name', 'author')->whereIn('value', $authors);
            });
        }

        // Fetch personalized articles
        $articles = $articlesQuery->with('attributes')->get();

        return response()->json($articles);
    }
}
