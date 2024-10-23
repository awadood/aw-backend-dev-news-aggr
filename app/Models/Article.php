<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Article",
 *     title="Article",
 *     description="Article model",
 *     @OA\Xml(
 *         name="Article"
 *     ),
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Breaking News: Example"),
 *     @OA\Property(property="content", type="string", example="This is the content of the news article."),
 *     @OA\Property(property="category", type="string", example="Technology"),
 *     @OA\Property(property="source", type="string", example="BBC News"),
 *     @OA\Property(property="author", type="string", example="Jane Doe"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */
class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'category',
        'source',
        'author',
        'published_at',
    ];
}
