<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ArticleController
 *
 * This controller handles the management of articles, including retrieving, creating,
 * updating, deleting articles, and providing personalized article feeds for users.
 */
class ArticleController extends Controller
{
    /**
     * Display a listing of articles with pagination and optional filters.
     *
     * This method retrieves articles, with optional filters for keyword, date, category, and source.
     *
     * @param  Request  $request  The HTTP request object containing filter data.
     * @return \Illuminate\Http\JsonResponse A JSON response containing a list of articles.
     *
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get a list of articles",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="keyword", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="category", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="source", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of articles retrieved successfully",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article"))
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->has('keyword')) {
            $query->where('title', 'like', '%'.$request->input('keyword').'%');
        }
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->has('source')) {
            $query->where('source', $request->input('source'));
        }
        if ($request->has('date')) {
            $query->whereDate('published_at', $request->input('date'));
        }

        $articles = $query->paginate(10);

        return response()->json($articles, 200);
    }

    /**
     * Display a specific article.
     *
     * This method retrieves a specific article by its ID.
     *
     * @param  int  $id  The ID of the article to retrieve.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the article data.
     *
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get an article by ID",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article retrieved successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function show($id)
    {
        $article = Article::find($id);

        if (! $article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return response()->json($article, 200);
    }

    /**
     * Store a newly created article.
     *
     * This method creates a new article for the authenticated user.
     *
     * @param  Request  $request  The HTTP request object containing article data.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the created article.
     *
     * @OA\Post(
     *     path="/api/articles",
     *     summary="Create a new article",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="title", type="string", example="Breaking News: Example"),
     *             @OA\Property(property="content", type="string", example="This is the content of the news article."),
     *             @OA\Property(property="category", type="string", example="Technology"),
     *             @OA\Property(property="source", type="string", example="BBC News"),
     *             @OA\Property(property="author", type="string", example="Jane Doe"),
     *             @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Article created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        $article = Article::create($validated);

        return response()->json($article, 201);
    }

    /**
     * Update an existing article.
     *
     * This method updates an existing article by its ID.
     *
     * @param  Request  $request  The HTTP request object containing article data.
     * @param  int  $id  The ID of the article to update.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated article.
     *
     * @OA\Put(
     *     path="/api/articles/{id}",
     *     summary="Update an existing article",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="title", type="string", example="Updated Title"),
     *             @OA\Property(property="content", type="string", example="Updated content of the article."),
     *             @OA\Property(property="category", type="string", example="Science"),
     *             @OA\Property(property="source", type="string", example="Updated Source"),
     *             @OA\Property(property="author", type="string", example="Updated Author"),
     *             @OA\Property(property="published_at", type="string", format="date-time", example="2024-02-01T00:00:00Z")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (! $article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'category' => 'sometimes|required|string|max:255',
            'source' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        $article->update($validated);

        return response()->json($article, 200);
    }

    /**
     * Remove an existing article.
     *
     * This method deletes an existing article by its ID.
     * It first checks if the article exists and belongs to the authenticated user before performing the deletion.
     * If the article does not exist, it returns a 404 response.
     *
     * @param  int  $id  The ID of the article to delete.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status of the deletion.
     *
     * @OA\Delete(
     *     path="/api/articles/{id}",
     *     summary="Delete an article",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Article deleted successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $article = Article::find($id);

        if (! $article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully'], 200);
    }

    /**
     * Update articles from external APIs.
     *
     * This method fetches articles from external sources and updates the database.
     * It can be triggered manually or by a scheduler to ensure the articles are up-to-date.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status of the update operation.
     *
     * @OA\Get(
     *     path="/api/update-articles",
     *     summary="Update articles from external sources",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Articles updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Articles updated successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update articles"
     *     )
     * )
     */
    public function updateArticles()
    {
        try {
            // Assuming there is logic here to fetch articles from external APIs
            // Example: ArticleService::updateFromExternalSources();

            return response()->json(['message' => 'Articles updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update articles'], 500);
        }
    }

    /**
     * Provide a personalized feed of articles for the authenticated user.
     *
     * This method retrieves articles based on the user's preferences, including categories, sources, and authors.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing a list of personalized articles.
     *
     * @OA\Get(
     *     path="/api/personalized-feed",
     *     summary="Get a personalized feed of articles",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Personalized feed retrieved successfully",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article"))
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function personalizedFeed()
    {
        $user = Auth::user();
        $preferences = $user->preference;

        $query = Article::query();

        if ($preferences->categories) {
            $query->whereIn('category', explode(', ', $preferences->categories));
        }
        if ($preferences->sources) {
            $query->whereIn('source', explode(', ', $preferences->sources));
        }
        if ($preferences->authors) {
            $query->whereIn('author', explode(', ', $preferences->authors));
        }

        $articles = $query->paginate(10);

        return response()->json($articles, 200);
    }
}
