<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Preference",
 *     title="Preference",
 *     description="Preference model",
 *
 *     @OA\Xml(
 *         name="Preference"
 *     ),
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="category, source, author"),
 *     @OA\Property(property="value", type="string", example="business, CNBC, Fred Imbert")\
 * )
 */
class Preference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'value',
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
