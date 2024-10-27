<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Exception;
use Illuminate\Http\Request;

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
        //
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
