<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $photo;

    public function __construct(User $user, $photo)
    {
        $this->user = $user;
        $this->photo = $photo;
    }

    public function handle()
    {
        Log::debug('cloudinary upload');
        $cloudinary = new CloudinaryService();

        Log::debug('photo', [
            'photo' => $this->photo
        ]);

        $photo = storage_path('app/' . $this->photo);

        Log::debug('photo upload', [
            'photo' => $photo
        ]);

        $uploadResult = $cloudinary->getCloudinary()->uploadApi()->upload($photo, [
            'folder' => 'users/photos'
        ]);
        Log::debug('passed');

        $this->user->update(['photo' => $uploadResult['secure_url']]);
    }
}
