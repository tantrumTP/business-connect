<?php

namespace App\Http\Controllers;

use App\Http\Resources\InfoRequestResource;
use App\Models\InfoRequest;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Schema;
use App\Models\Business;
use App\Models\Product;
use App\Models\Service;

class InfoRequestController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'business_id' => 'nullable|integer|exists:businesses,id|required_without_all:product_id,service_id',
                'product_id' => 'nullable|integer|exists:products,id|required_without_all:business_id,service_id',
                'service_id' => 'nullable|integer|exists:services,id|required_without_all:business_id,product_id',
                'user_id' => 'nullable|integer|exists:users,id',
            ], [
                'required_without_all' => 'You must provide at least one of business_id, product_id, or service_id.',
            ]);

            $query = InfoRequest::query();
            $user = $this->getUser();

            if (!empty($validated['business_id'])) {
                // Check business belongs to authenticated user.
                $user->businesses()->findOrFail($validated['business_id']);

                $query->where(function ($q) use ($validated) {
                    $q->where('requestable_type', Business::class)
                        ->where('requestable_id', $validated['business_id']);
                    if (!empty($validated['user_id'])) {
                        $q->where('user_id', $validated['user_id']);
                    }
                })->orWhere(function ($q) use ($validated) {
                    // InfoRequests associated to products that belong to business
                    $q->where('requestable_type', Product::class)
                        ->whereHasMorph('requestable', [Product::class], function ($query) use ($validated) {
                            $query->whereHas('business', function ($q) use ($validated) {
                                $q->where('id', $validated['business_id']);
                            });
                        });
                    if (!empty($validated['user_id'])) {
                        $q->where('user_id', $validated['user_id']);
                    }
                })->orWhere(function ($q) use ($validated) {
                    // InfoRequests asociadas a servicios del negocio
                    $q->where('requestable_type', Service::class)
                        ->whereHasMorph('requestable', [Service::class], function ($query) use ($validated) {
                            $query->whereHas('business', function ($q) use ($validated) {
                                $q->where('id', $validated['business_id']);
                            });
                        });
                    if (!empty($validated['user_id'])) {
                        $q->where('user_id', $validated['user_id']);
                    }
                });
            } elseif (!empty($validated['product_id'])) {
                // Check if product belongs to business that belongs to authenticated user.
                Product::whereHas('business', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->findOrFail($validated['product_id']);

                $query->where('requestable_type', Product::class)
                    ->where('requestable_id', $validated['product_id']);
            } elseif (!empty($validated['service_id'])) {
                // Check if service belongs to business that belongs to authenticated user.
                Service::whereHas('business', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->findOrFail($validated['service_id']);

                $query->where('requestable_type', Service::class)
                    ->where('requestable_id', $validated['service_id']);
            }

            if (!empty($validated['user_id'])) {
                $query->where('user_id', $validated['user_id']);
            }

            $infoRequests = $query->with(['requestable'])->paginate(15);

            $response = $this->sendResponse(InfoRequestResource::collection($infoRequests)->response()->getData(true));
        } catch (Exception $e) {
            $response = $this->sendError('Error retrieving info requests', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'requestable_id' => 'required|integer',
                'requestable_type' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
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
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'message' => 'required|string'
            ]);

            $infoRequest = new InfoRequest($validatedData);

            $user = $this->getUser();
            if ($user) {
                $infoRequest->user()->associate($user);
            }

            if (!empty($validatedData['requestable_id']) && !empty($validatedData['requestable_type'])) {
                $modelClass = "App\\Models\\{$validatedData['requestable_type']}";
                $requestableModel = $modelClass::findOrFail($validatedData['requestable_id']);
                $infoRequest->requestable_type = $modelClass;
                $infoRequest->requestable()->associate($requestableModel);
            }

            $infoRequest->save();

            $response = $this->sendResponse(new InfoRequestResource($infoRequest), 'Info request created successfully.');
        } catch (Exception $e) {
            $response = $this->sendError('Error creating info request', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->getUser();
            $infoRequest = InfoRequest::findOrFail($id);
            // Check if the info request belongs to business that belongs to the user
            $requestableModel = $infoRequest->requestable;

            switch ($infoRequest->requestable_type) {
                case 'App\Models\Business':
                    $user->businesses()->findOrFail($requestableModel->id);
                    break;
                case 'App\Models\Product':
                case 'App\Models\Service':
                    $user->businesses()->findOrFail($requestableModel->business_id);
                    break;
                default:
                    abort(404, "There is no model of the type: {$infoRequest->requestable_type}");
                    break;
            }

            $response = $this->sendResponse(new InfoRequestResource($infoRequest));
        } catch (Exception $e) {
            $response = $this->sendError('Error retrieving info request', ['exceptionMessage' => $e->getMessage()], 422);
        }
        return $response;
    }
}
