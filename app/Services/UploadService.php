<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Upload an image to a specified storage location.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    public function uploadImage($file, $path = 'uploads')
    {
        // Store the image and return the path where it was stored
        $filePath = $file->store($path, 'public');

        return $filePath;
    }
}
