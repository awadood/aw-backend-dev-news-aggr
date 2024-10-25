<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     description="Article model",
 *     required={"title", "url", "content"},
 *
 *     @OA\Property(property="id", type="integer", readOnly=true, description="The unique identifier of the article"),
 *     @OA\Property(property="title", type="string", description="The title of the article"),
 *     @OA\Property(property="url", type="string", description="The URL of the article"),
 *     @OA\Property(property="content", type="string", description="The content of the article"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, description="The creation timestamp of the article"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true, description="The update timestamp of the article")
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
        'url',
        'content',
    ];

    /**
     * Get the attributes associated with the article.
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }
}
