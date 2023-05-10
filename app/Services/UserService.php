<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserService
{

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $request['password'] = Hash::make($request['password']);
            $request['remember_token'] = Str::random(10);
            $request['type'] = $request['type'] ? $request['type']  : 0;

            $user = User::create($request->toArray());

            $imageService = new ImageService();

            $imageObject = $imageService->storeImage($request->file('avatar'), 'avatars');

            $image = new Image($imageObject);

            $user->image()->save($image);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return [null,  422, 'error', ['Something went wrong. Please try again later!']];
        }
    }
}
