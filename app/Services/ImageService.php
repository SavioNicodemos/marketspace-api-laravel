<?php

namespace App\Services;

class ImageService
{

    public function storeImage($imageFromRequest, string $folder)
    {
        $originalName = $imageFromRequest->getClientOriginalName();
        $extension = $imageFromRequest->getClientOriginalExtension();

        $md5Name = md5_file($imageFromRequest->getRealPath());
        $guessExtension = $imageFromRequest->guessExtension();

        $newName = $md5Name . '.' . $guessExtension;
        $imageFromRequest->storeAs($folder, $newName, 'public');

        return [
            'name' => $newName,
            'original_name' => $originalName,
            'format' => $extension
        ];
    }
}
