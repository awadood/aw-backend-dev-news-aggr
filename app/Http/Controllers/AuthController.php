<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthController
 *
 * This controller handles user authentication, including registration,
 * login, logout, and password reset operations.
 *
 * @OA\Info(
 *     title="Authentication API",
 *     version="1.0.0",
 *     description="API documentation for operations of user registration, login, logout, and password reset"
 * )
 */
class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * This method handles the registration of a new user by validating the input data,
     * creating a user record, and generating an authentication token.
     *
     * @param Request $request The HTTP request object containing user registration data.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the access token and token type.
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password1234"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login an existing user.
     *
     * This method handles user login by validating the credentials and generating
     * an authentication token if successful.
     *
     * @param Request $request The HTTP request object containing login data.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the access token and token type.
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login an existing user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged in",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    /**
     * Logout the authenticated user.
     *
     * This method handles the logout process for an authenticated user by deleting
     * the user's current access token.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the logout status.
     *
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => __('auth.logged_out')], 200);
    }

    /**
     * Handle password reset request.
     *
     * This method handles the password reset process by sending a password reset link
     * to the user's email address.
     *
     * @param Request $request The HTTP request object containing the user's email.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status of the reset link.
     *
     * @OA\Post(
     *     path="/api/password/reset",
     *     summary="Handle password reset request",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="We have emailed your password reset link!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error sending reset link"
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            event(new PasswordReset(Auth::user()));

            return response()->json(['message' => __($status)], 200);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
