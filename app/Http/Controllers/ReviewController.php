<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReviewController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $reviews = Review::paginate(15);
            $response = $this->sendResponse(ReviewResource::collection($reviews)->response()->getData(true));
        } catch (Exception $e) {
            $response = $this->sendError('Error retrieving reviews', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $reviewData = $request->validate([
                'rating' => 'required|integer',
                'content' => 'required|string',
                'reviewable_id' => 'required|integer',
                'reviewable_type' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $validModels = [
                            'Business',
                            'Product',
                            'Service',
                        ];
                        $validClass = "App\\Models\\{$value}";
                        // Check if is valid model
                        if (!in_array($value, $validModels) || !class_exists($validClass)) {
                            $fail("The {$attribute} must be a valid model type.");
                            return;
                        }
                        // Check if model exists
                        try {
                            $model = new $validClass;
                            if (!Schema::hasTable($model->getTable())) {
                                $fail("The table for {$attribute} does not exist.");
                            }
                        } catch (Exception $e) {
                            $fail("Error creating instance of {$attribute}: " . $e->getMessage());
                        }  
                    },
                ],
            ]);
            //Check if instance of model exists
            $reviewableModel = "App\\Models\\{$reviewData['reviewable_type']}"::findOrFail($reviewData['reviewable_id']);
            $reviewData['user_id'] = $this->getUser()->id;
            $review = $reviewableModel->reviews()->create($reviewData);
            $response = $this->sendResponse(new ReviewResource($review), 'Review created sucessfully', 201);
        } catch (Exception $e) {
            $response = $this->sendError('Error on create review', ['messageException' => $e->getMessage()], 400);
        }
        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $review = Review::findOrFail($id);
            $response = $this->sendResponse(new ReviewResource($review));
        } catch (Exception $e) {
            $response = $this->sendError('Error retrieving review', ['exceptionMessage' => $e->getMessage()], 404);
        }
        return $response;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $reviewData = $request->validate([
                'rating' => 'required|integer',
                'content' => 'required|string',
            ]);
            
            $updateData = $request->only(['rating', 'content']);
            // Check if the review belongs to authenticated user
            $review = $this->getUser()->reviews()->findOrFail($id);
            $review->update($updateData);
            $response = $this->sendResponse(new ReviewResource($review), 'Review updated sucessfully');
        }catch (ModelNotFoundException $e) {
            $response = $this->sendError('Error updating review', ['exceptionMessage' => 'The review not exists'], 422);
        } catch (Exception $e) {
            $response = $this->sendError('Error updating review', ['exceptionMessage' => $e->getMessage()], 400);
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //TODO: Do not completely remove, mark as inactive and do not show for at least 30 days, then remove
        try {
            $review = $this->getUser()->reviews()->findOrFail($id);
            $review->delete();
            $response = $this->sendResponse(['id' => $id], 'Review removed sucessfully');
        }catch (ModelNotFoundException $e) {
            $response = $this->sendError('Error deleting review', ['exceptionMessage' => 'The review not exists'], 422);
        } catch (Exception $e) {
            $response = $this->sendError('Error deleting review', ['exceptionMessage' => $e->getMessage()], 400);
        }
        return $response;
    }
}
