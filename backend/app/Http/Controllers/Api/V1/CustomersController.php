<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomersController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $customers = Customer::paginate(20);

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return new CustomerResource($customer)->response()->setStatusCode(201);
    }

    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(null, 204);
    }
}
