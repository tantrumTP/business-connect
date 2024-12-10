<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Business;

class ServiceController extends BaseController
{

    /**
     * Get Services related with business
     */
    public function index(string $businessId)
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $serviceData = $request->validate([
                'business_id' => 'required|exists:businesses,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'category' => 'nullable|string|max:255',
                'duration' => 'nullable|integer',
                'status' => 'sometimes|required|boolean',
                'path_alias' => 'nullable|string|max:255|unique:path_aliases,alias'
            ]);
            $business = $this->getUser()->businesses()->findOrFail($serviceData['business_id']);
            $serviceCreated = $business->services()->create($serviceData);

            if ($serviceData['path_alias']) {
                $serviceCreated->createPathAlias($serviceData['path_alias']);
            }

            DB::commit();

            $response = $this->sendResponse(new ServiceResource($serviceCreated), 'Service created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->sendError('Error creating service', ['exceptionMessage' => $e->getMessage()]);
        }
        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $service = Service::findOrFail($id);
            $response = $this->sendResponse(new ServiceResource($service));
        } catch (Exception $e) {
            $response = $this->sendError('Error retrieving service', ['exceptionMessage' => $e->getMessage()], 404);
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
            $service = Service::findOrFail($id);
            // Check if service belongs to the user's business
            $business = $this->getUser()->businesses()->findOrFail($service->business_id);
            $serviceData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'category' => 'required|string|max:255',
                'duration' => 'nullable|integer',
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
            $service->update($serviceData);

            if ($serviceData['path_alias']) {
                $service->createPathAlias($serviceData['path_alias']);
            }

            DB::commit();

            $response = $this->sendResponse(new ServiceResource($service), 'Service updated sucessfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $response = $this->sendError('Error updating service', ['exceptionMessage' => 'The service not exists'], 422);
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->sendError('Error updating service', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $service = Service::findOrFail($id);
            // Check if service belongs to the user's business
            $business = $this->getUser()->businesses()->findOrFail($service->business_id);
            $service->status = false;
            $service->save();
            $service->delete($service);
            $response = $this->sendResponse(['id' => $id], 'Service removed sucessfully');
        } catch (ModelNotFoundException $e) {
            $response = $this->sendError('Error updating service', ['exceptionMessage' => 'The service not exists'], 422);
        } catch (Exception $e) {
            $response = $this->sendError('Error updating service', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }
}
