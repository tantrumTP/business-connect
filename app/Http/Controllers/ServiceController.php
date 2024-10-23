<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;

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
                'duration' => 'nullable|string',
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
