<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

trait HandleMediaTrait
{
    protected function handleMediaUpload($model, Request $request, $fieldName = 'media')
    {
        $allFiles = $request->allFiles();
        if (isset($allFiles[$fieldName]) && is_array($allFiles[$fieldName])) {
            foreach ($allFiles[$fieldName] as $index => $mediaItem) {
                if (isset($mediaItem['file']) && $mediaItem['file'] instanceof UploadedFile) {
                    $file = $mediaItem['file'];
                    $path = $file->store("media/{$model->getTable()}", 'public');
                    $model->media()->create([
                        'type' => $request->input("{$fieldName}.{$index}.type"),
                        'file_path' => $path,
                        'caption' => $request->input("{$fieldName}.{$index}.caption"),
                    ]);
                }
            }
        }
    }
}
