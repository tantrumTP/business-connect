<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\PathAlias;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $productData = $request->validate([
                'business_id' => 'required|exists:businesses,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'category' => 'nullable|string|max:255',
                'availability' => 'nullable|boolean',
                'warranty' => 'nullable|string',
                'status' => 'sometimes|required|boolean',
                'path_alias' => 'nullable|string|max:255|unique:path_aliases,alias'
            ]);
            $business = $this->getUser()->businesses()->findOrFail($productData['business_id']);
            $productCreated = $business->products()->create($productData);
            
            if(!empty($productData['path_alias'])){
                $productCreated->createPathAlias($productData['path_alias']);
            }

            DB::commit();
            
            $response = $this->sendResponse(new ProductResource($productCreated), 'Product created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->sendError('Error creating product', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $response = $this->sendResponse(new ProductResource($product));
        } catch (Exception $e) {
            $response = $this->sendError('Error retrieving product', ['exceptionMessage' => $e->getMessage()], 404);
        }
        return $response;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            //Check if the business with which the product is related belongs to the authenticated user
            $business = $this->getUser()->businesses()->findOrFail($product->business_id);
            $productData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'category' => 'nullable|string|max:255',
                'availability' => 'nullable|boolean',
                'warranty' => 'nullable|string',
                'status' => 'sometimes|required|boolean',
                'path_alias' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('path_aliases', 'alias')->ignore(
                        $product->getPathAlias()->id ?? null
                    )
                ]
            ]);
            $product->update($productData);

            if(!empty($productData['path_alias'])){
                $product->createPathAlias($productData['path_alias']);
            }

            DB::commit();

            $response = $this->sendResponse(new ProductResource($product), 'Product updated successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $response = $this->sendError('Error updating product', ['exceptionMessage' => 'The product not exists'], 422);
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->sendError('Error updating product', ['exceptionMessage' => $e->getMessage()], 422);
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
            $product = Product::findOrFail($id);
            //Check if the business with which the product is related belongs to the authenticated user
            $business = $this->getUser()->businesses()->findOrFail($product->business_id);
            if ($business) {
                $product->delete();
                $response = $this->sendResponse(['id' => $id], 'Product removed successfully');
            }
        } catch (ModelNotFoundException $e) {
            $response = $this->sendError('Error deleting product', ['exceptionMessage' => 'The product not exists'], 422);
        } catch (Exception $e) {
            $response = $this->sendError('Error deleting product', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }
}
