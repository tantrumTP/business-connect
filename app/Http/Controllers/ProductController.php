<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $productData = $request->validate([
                'business_id' => 'required|exists:businesses,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'category' => 'nullable|string|max:255',
                'availability' => 'nullable|boolean',
                'warranty' => 'nullable|string',
                'status' => 'sometimes|required|in:active,inactive'
            ]);
            $business = $this->getUser()->businesses()->findOrFail($productData['business_id']);
            $productCreated = $business->products()->create($productData);
            $response = $this->sendResponse(new ProductResource($productCreated), 'Product created successfully');
        } catch (Exception $e) {
            $response = $this->sendError('Error creating product', ['exceptionMessage' => $e->getMessage()], 422);
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
