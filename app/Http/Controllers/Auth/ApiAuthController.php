<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\{Hash, Validator, Password};
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    use ApiResponser;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function me()
    {
        $userId = auth()->user()->id;

        $userData = $this->userService->getUserData($userId);
        return $userData;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'tel' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'avatar' => 'required|image'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $this->userService->create($request);
        return response(null, 201);
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
        return $this->successResponse($response, 200);
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
