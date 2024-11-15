<?php

namespace App\Http\Controllers;

use App\Http\Resources\BusinessResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServiceResource;
use App\Models\Business;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\HandleMediaTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BusinessController extends BaseController
{

    use HandleMediaTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $businesses = $this->getUser()->businesses()->paginate(15);
        return $this->sendResponse(BusinessResource::collection($businesses)->response()->getData(true));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'direction' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'required|email|max:255',
                'hours' => 'required|array',
                'website' => 'nullable|url|max:255',
                'social_networks' => 'nullable|array',
                'characteristics' => 'nullable|array',
                'covered_areas' => 'nullable|array',
                'media' => 'nullable|array',
                'media.*.file' => 'required|file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:20480',
                'media.*.type' => 'required|in:image,video',
                'media.*.caption' => 'nullable|string|max:255',
                'path_alias' => 'nullable|string|max:255|unique:path_aliases,alias'
            ]);
            $business = $this->getUser()->businesses()->create($validatedData);

            // Process media files
            $this->handleMediaUpload($business, $request);

            if ($validatedData['path_alias']) {
                $business->createPathAlias($validatedData['path_alias']);
            }

            DB::commit();

            $response = $this->sendResponse(new BusinessResource($business), 'Business successfully created.');
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->sendError('Error on Business store', ['exceptionMessage' => $e->getMessage()], 422);
        }

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $response = false;
        try {
            $business = Business::findOrFail($id);
            $mediaPaginator = $business->media()->paginate(10);

            $businessResource = new BusinessResource($business);
            $businessResource->additional(['media' => $mediaPaginator]);

            $response = $this->sendResponse($businessResource);
        } catch (Exception $e) {
            $response = $this->sendError('Error consulting business', ['exceptionMessage' => $e->getMessage()], 422);
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
            $business = $this->getUser()->businesses()->findOrFail($id);
            $attributes = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'direction' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'sometimes|required|email|max:255',
                'hours' => 'sometimes|required|array',
                'website' => 'nullable|url|max:255',
                'social_networks' => 'nullable|array',
                'characteristics' => 'nullable|array',
                'covered_areas' => 'nullable|array',
                'status' => 'sometimes|required|boolean',
                'path_alias' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('path_aliases', 'alias')->ignore(
                        $business->getPathAlias()->id ?? null
                    )
                ]
            ]);

            $business->update($attributes);

            if ($attributes['path_alias']) {
                $business->createPathAlias($attributes['path_alias']);
            }

            DB::commit();

            $response = $this->sendResponse(new BusinessResource($business), 'Business updated succesfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $response = $this->sendError('Error updating business', ['exceptionMessage' => 'The business not exists'], 422);
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->sendError('Error updating business', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $business = $this->getUser()->businesses()->findOrFail($id);
            $business->status = false;
            $business->save();
            $business->delete();
            $response = $this->sendResponse(['id' => $id], 'Business remove succesfully');
        } catch (Exception $e) {
            $response = $this->sendError('Error deleting business', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }


    /**
     * Get Products related with business
     */
    public function getProducts(string $businessId)
    {
        try {
            $business = Business::findOrFail($businessId);
            $products = $business->products()->paginate(15);
            $response = $this->sendResponse(ProductResource::collection($products)->response()->getData(true));
        } catch (Exception $e) {
            $response = $this->sendError("Error retrieving products of business with ID: {$business->id}", ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }

    /**
     * Get Services related with business
     */
    public function getServices(string $businessId)
    {
        try {
            $business = Business::findOrFail($businessId);
            $services = $business->services()->paginate(15);
            $response = $this->sendResponse(ServiceResource::collection($services)->response()->getData(true));
        } catch (Exception $e) {
            $response = $this->sendError("Error retrieving services of business with ID: {$business->id}", ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }
}
