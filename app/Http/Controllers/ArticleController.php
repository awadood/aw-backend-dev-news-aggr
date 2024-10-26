<?php

namespace App\Http\Controllers;

use App\Http\Requests\FetchArticlesRequest;
use App\Models\Article;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\JsonResponse;

/**
 * Class ArticleController
 *
 * This controller handles the management of articles, including retrieving, creating,
 * updating, deleting articles, and providing personalized article feeds for users.
 */
class ArticleController extends Controller
{
    /**
     * Fetch articles with support for pagination and search functionality.
     *
     * This endpoint allows users to retrieve a paginated list of articles. Users can also filter
     * articles by keyword, date, category, or source using query parameters.
     *
     * @param  App\Http\Requests\FetchArticlesRequest  $request
     *
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Fetch paginated articles",
     *     description="Retrieve a paginated list of articles with optional search filters for keyword, date, category, and source.",
     *     operationId="getArticles",
     *     tags={"Articles"},
     *
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Filter articles by keyword in the attributes",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter articles by date (YYYY-MM-DD)",
     *
     *         @OA\Schema(type="string", format="date")
     *     ),
     *
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter articles by category",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter articles by source",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with paginated articles",
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
     */
    public function index(FetchArticlesRequest $request): JsonResponse
    {
        $query = Article::query()->with('attributes');

        // Apply filters based on query parameters
        if ($request->has('keyword')) {
            $keyword = $request->input('keyword');
            $query->whereHas('attributes', function ($q) use ($keyword) {
                $q->where('name', 'keyword')->where('value', 'like', "%$keyword%");
            });
        }

        if ($request->has('date')) {
            $date = $request->input('date');
            $query->whereHas('attributes', function ($q) use ($date) {
                $q->where('name', 'date')->where('value', 'like', "$date%");
            });
        }

        if ($request->has('category')) {
            $category = $request->input('category');
            $query->whereHas('attributes', function ($q) use ($category) {
                $q->where('name', 'category')->where('value', $category);
            });
        }

        if ($request->has('source')) {
            $source = $request->input('source');
            $query->whereHas('attributes', function ($q) use ($source) {
                $q->where('name', 'source')->where('value', $source);
            });
        }

        $articles = $query->paginate(config('articles.page_size'))
            ->through(fn(Article $article, int $key) => fractal($article, new ArticleTransformer)->toArray());

        return response()->json($articles);
    }

    /**
     * Retrieve details for a single article.
     *
     * This endpoint allows users to retrieve the details of a specific article by its ID.
     *
     *
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get single article details",
     *     description="Retrieve the details of a specific article by its ID.",
     *     operationId="getArticleById",
     *     tags={"Articles"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to retrieve",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with article details",
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
    public function show(int $id): JsonResponse
    {
        $article = Article::with('attributes')->find($id);

        if (! $article) {
            return response()->json(['error' => 'Article not found.'], 404);
        }

        return response()->json(fractal($article, new ArticleTransformer));
    }
}
