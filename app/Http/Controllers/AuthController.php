<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;


class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     )
     * )
     */
    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();
        $data['role'] = 'user';
        $user = User::create($data);
        $user->sendEmailVerificationNotification();
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->apiSuccess("User registered successfully", $user, $token);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login a user",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return $this->apiError("User not found");
        }
        if (!Hash::check($credentials['password'], $user->password)) {
            return $this->apiError("Invalid credentials");
        }
        $user->FCM_token = $request['FCM_token'];
        $user->save();
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->email_verified_at = $user->hasVerifiedEmail();
        return $this->apiSuccess("User login successfully", $user, $token);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout a user",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->apiSuccess("User logged out successfully");
    }
}
