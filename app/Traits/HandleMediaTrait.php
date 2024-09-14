<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HandleMediaTrait
{
    protected function handleMediaUpload($model, Request $request, $fieldName = 'media')
    {
        if ($request->hasFile($fieldName)) {
            foreach ($request->file($fieldName) as $index => $mediaFile) {
                $path = $mediaFile->store("media/{$model->getTable()}", 'public');
                $model->media()->create([
                    'type' => $request->input("{$fieldName}.{$index}.type"),
                    'file_path' => $path,
                    'caption' => $request->input("{$fieldName}.{$index}.caption"),
                ]);
            }
        }
    }
}
