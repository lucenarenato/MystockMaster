<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    public static function upload($file, $directory)
    {
        // Generate a unique filename to avoid conflicts
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        // Store the file in the specified directory
        Storage::disk('public')->put($directory . '/' . $filename, file_get_contents($file));

        return '/storage/' . $directory . '/' . $filename;
    }
}
