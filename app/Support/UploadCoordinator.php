<?php

namespace App\Support;

use Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadCoordinator
{
    /**
     * Upload to the public directory
     *
     * @param mixed $file
     * @param string $directory
     *
     * @return string
     */
    public function publicUpload($file, $directory)
    {
        $path = $file->hashName($directory);

        $file->storePublicly(
            sprintf(
                'public/%s',
                $directory
            )
        );

        return url(Storage::disk('public')->url($path));
    }
}