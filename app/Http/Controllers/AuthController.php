<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Password};

class AuthController extends Controller
{
    use ApiResponser;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function me(): JsonResponse
    {
        $userId = auth()->user()->id;

        return $this->successResponse($this->userService->getUserData($userId));
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'tel' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'avatar' => 'required|image'
        ]);

        $this->userService->create($validated);
        return $this->successResponse(null, 201);
    }

    /**
     * @throws NotFoundException
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            throw new NotFoundException('User');
        }
        if (!Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('Password mismatch', 422);
        }

        $token = $user->createToken('web')->plainTextToken;
        return $this->successResponse(['token' => $token]);
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
            'email' => 'required|email'
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
