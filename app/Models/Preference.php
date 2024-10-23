<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Preference",
 *     description="Preference model",
 *     @OA\Xml(
 *         name="Preference"
 *     )
 * )
 */
class Preference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * @OA\Property(property="user_id", type="integer", example="1")
     * @OA\Property(property="sources", type="string", example="BBC, CNN")
     * @OA\Property(property="categories", type="string", example="Technology, Sports")
     * @OA\Property(property="authors", type="string", example="John Doe, Jane Smith")
     */
    protected $fillable = [
        'user_id',
        'sources',
        'categories',
        'authors',
    ];

    /**
     * Get the user that owns the preferences.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
