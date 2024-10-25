<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class PreferenceController
 *
 * This controller handles user preferences, allowing users to create, update, view, and delete
 * their preferences for news sources, categories, and authors.
 */
class PreferenceController extends Controller
{
    /**
     * Display the user's preferences.
     *
     * This method retrieves the preferences for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the user's preferences.
     *
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get the user's preferences",
     *     tags={"Preferences"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Preference")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();

        return response()->json($user->preferences, 200);
    }

    /**
     * Store or update the user's preferences.
     *
     * This method creates or updates the preferences for the authenticated user.
     *
     * @param  Request  $request  The HTTP request object containing preference data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status of the operation.
     *
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Create or update the user's preferences",
     *     tags={"Preferences"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="sources", type="string", example="BBC, CNN"),
     *             @OA\Property(property="categories", type="string", example="Technology, Sports"),
     *             @OA\Property(property="authors", type="string", example="John Doe, Jane Smith")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User preferences updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Preference")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function storeOrUpdate(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'sources' => 'nullable|string',
            'categories' => 'nullable|string',
            'authors' => 'nullable|string',
        ]);

        $preference = Preference::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json($preference, 200);
    }

    /**
     * Delete the user's preferences.
     *
     * This method deletes the preferences for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status of the deletion.
     *
     * @OA\Delete(
     *     path="/api/preferences",
     *     summary="Delete the user's preferences",
     *     tags={"Preferences"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User preferences deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Preferences deleted successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy()
    {
        $user = Auth::user();
        $user->preferences()->delete();

        return response()->json(['message' => __('aggregator.preference.deleted')], 200);
    }
}
