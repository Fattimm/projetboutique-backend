<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\UploadPhotoJob;

class UploadPhotoListener
{
    public function handle(ClientCreated $event)
    {
        if (isset($event->user->photo)) {
            // Dispatch du job d'upload photo
            UploadPhotoJob::dispatch($event->user, $event->user->photo);

        }
    }
}
