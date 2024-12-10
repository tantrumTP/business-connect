<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Schema;

class MediaController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:20480', // 20MB max
                'mediaable_type' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $validModels = [
                            'App\\Models\\Business',
                            'App\\Models\\Product',
                            'App\\Models\\Service',
                        ];
                        // Check if is valid model
                        if (!in_array($value, $validModels) || !class_exists($value)) {
                            $fail("The {$attribute} must be a valid model type.");
                        }
                        // Check if model exists
                        $model = new $value;
                        if (!Schema::hasTable($model->getTable())) {
                            $fail("The table for {$attribute} does not exist.");
                        }
                    },
                ],
                'mediaable_id' => 'required|integer',
                'type' => 'required|in:image,video',
                'caption' => 'nullable|string|max:255',
            ]);

            $file = $request->file('file');
            $path = $file->store('media', 'public');

            $media = Media::create([
                'mediaable_type' => $request->mediaable_type,
                'mediaable_id' => $request->mediaable_id,
                'type' => $request->type,
                'file_path' => $path,
                'caption' => $request->caption,
            ]);

            $response = $this->sendResponse($media, 'Media stored sucessfully', 201);
        } catch (Exception $e) {
            $response = $this->sendError('Error on media store', [$e->getMessage()], 409);
        }

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
