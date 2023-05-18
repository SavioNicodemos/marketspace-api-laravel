<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\UuidInterface;

class AuthService
{

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    #[ArrayShape(['token' => "\Laravel\Sanctum\string|string", 'user' => "mixed", 'refresh_token' => "\Ramsey\Uuid\UuidInterface"])]
    public function loginWithPasswordAndEmail(array $validated): array
    {
        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            throw new NotFoundException('User');
        }
        if (!Hash::check($validated['password'], $user->password)) {
            throw new Exception('Password Mismatch');
        }

        $token = $user->createToken('web')->plainTextToken;
        $refreshToken = $this->createRefreshToken($user->id);
        return [
            'token' => $token,
            'user' => $user,
            'refresh_token' => $refreshToken,
        ];
    }

    public function createRefreshToken(string $userId): UuidInterface
    {
        $carbon = new Carbon();
        $now = $carbon->now();
        $refreshTokenId = Str::uuid();
        DB::table('refresh_tokens')->insert([
            'id' => $refreshTokenId,
            'expires_in' => $now->addMinutes(config('auth.refresh_token_ttl'))->unix(),
            'user_id' => $userId,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        return $refreshTokenId;
    }
}
