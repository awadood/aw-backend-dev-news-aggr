<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Attribute",
 *     type="object",
 *     title="Attribute",
 *     description="Attribute model",
 *     required={"article_id", "name", "value"},
 *
 *     @OA\Property(property="id", type="integer", readOnly=true, description="The unique identifier of the attribute"),
 *     @OA\Property(property="article_id", type="integer", description="The ID of the related article"),
 *     @OA\Property(property="name", type="string", description="The name of the attribute"),
 *     @OA\Property(property="value", type="string", description="The value of the attribute"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, description="The creation timestamp of the attribute"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true, description="The update timestamp of the attribute")
 * )
 */
class Attribute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_id',
        'name',
        'value',
    ];

    /**
     * Get the article that owns the attribute.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
