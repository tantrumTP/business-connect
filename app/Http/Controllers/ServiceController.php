<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Models\Service;

class ServiceController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $serviceData = $request->validate([
                'business_id' => 'required|exists:businesses,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'category' => 'nullable|string|max:255',
                'duration' => 'nullable|integer',
                'status' => 'sometimes|required|in:active,inactive'
            ]);
            $business = $this->getUser()->businesses()->findOrFail($serviceData['business_id']);
            $serviceCreated = $business->services()->create($serviceData);
            $response = $this->sendResponse(new ServiceResource($serviceCreated), 'Service created successfully');
        } catch (Exception $e) {
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
                'status' => 'sometimes|required|in:active,inactive',
            ]);
            $service->update($serviceData);
            $response = $this->sendResponse(new ServiceResource($service), 'Service updated sucessfully');
        } catch (ModelNotFoundException $e) {
            $response = $this->sendError('Error updating service', ['exceptionMessage' => 'The service not exists'], 422);
        } catch (Exception $e) {
            $response = $this->sendError('Error updating service', ['exceptionMessage' => $e->getMessage()], 422);
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
            $service = Service::findOrFail($id);
            // Check if service belongs to the user's business
            $business = $this->getUser()->businesses()->findOrFail($service->business_id);
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
