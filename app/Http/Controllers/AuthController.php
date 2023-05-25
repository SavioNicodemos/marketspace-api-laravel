<?php

namespace App\Http\Controllers;

use App\Exceptions\ApplicationException;
use App\Exceptions\NotFoundException;
use App\Services\AuthService;
use App\Services\UserService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    use ApiResponser;

    protected UserService $userService;

    protected AuthService $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function me(): JsonResponse
    {
        $userId = auth()->user()->id;

        return $this->successResponse($this->userService->getUserData($userId));
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'tel' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'avatar' => 'required|image',
        ]);

        $this->userService->create($request);

        return $this->successResponse(null, 201);
    }

    /**
     * @throws NotFoundException|ApplicationException
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        return $this->successResponse($this->authService->loginWithPasswordAndEmail($validated));
    }

    /**
     * @throws ApplicationException
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'refresh_token' => 'required|uuid',
        ]);

        return $this->successResponse($this->authService->refreshToken($validated));
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        $token->delete();
        $response = ['message' => 'You have been successfully logged out!'];

        return $this->successResponse($response);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $response = Password::sendResetLink($validated);

        $message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : 'GLOBAL_SOMETHING_WANTS_TO_WRONG';

        return $this->successResponse($message);
    }

    public function passwordReset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = Password::reset($validated, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
            $user->tokens()->delete();
        });

        $message = $response == Password::PASSWORD_RESET ? 'Password reset successfully' : 'GLOBAL_SOMETHING_WANTS_TO_WRONG';

        return $this->successResponse($message);
    }
}
