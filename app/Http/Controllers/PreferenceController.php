<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreferencesRequest;
use App\Models\Preference;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PreferenceController handles user preferences based on attributes.
 *
 * This controller provides endpoints for users to set and retrieve their preferred
 * news settings, such as sources, categories, and authors, allowing the application
 * to customize the user's news feed based on their interests.
 */
class PreferenceController extends Controller
{
    /**
     * Retrieve user preferences for articles.
     *
     * This endpoint allows users to get their current preferences for articles.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response
     *
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get user preferences",
     *     description="Retrieve user preferences for news sources, categories, and authors.",
     *     operationId="indexUserPreferences",
     *     tags={"Preferences"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with user preferences",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *                 type="object",
     *
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="value", type="string")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $preferences = Preference::where('user_id', $user->id)->get(['name', 'value']);

        return response()->json($preferences);
    }

    /**
     * Set user preferences for articles.
     *
     * This endpoint allows users to set their preferences for articles.
     *
     * @param  App\Http\Requests\PreferencesRequest  $request  The form request.
     * @return \Illuminate\Http\JsonResponse A JSON response
     *
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Store user preferences",
     *     description="Create or update user preferences for user preferences",
     *     operationId="storeUserPreferences",
     *     tags={"Preferences"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="preferences", type="array",
     *
     *                 @OA\Items(type="object",
     *
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="value", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update preferences"
     *     )
     * )
     */
    public function store(PreferencesRequest $request): JsonResponse
    {
        $preferences = $request->validated()['preferences'];
        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Remove all existing preferences for the user
            Preference::where('user_id', $user->id)->delete();

            foreach ($preferences as $preference) {
                Preference::create([
                    'user_id' => $user->id,
                    'name' => $preference['name'],
                    'value' => $preference['value'],
                ]);
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return response()->json(['error' => __('aggregator.preference.failed')], 500);
        }

        return response()->json(['message' => __('aggregator.preference.stored')]);
    }
}
