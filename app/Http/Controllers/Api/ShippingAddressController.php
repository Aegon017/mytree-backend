<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;

/**
 * @category Controller
 * @package ShippingAddressController
 */

/**
 * @OA\Tag(name="Ecommerce", description="Operations related to Ecommerce")
 */
class ShippingAddressController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     path="/api/shipping-addresses",
     *     summary="Get a list of all shipping addresses for the authenticated user",
     *     tags={"Ecommerce"},
     *     security={{"bearer": {}}},
     *     @OA\Response(response=200, description="List of shipping addresses", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $user = Auth::id();
        $addresses = ShippingAddress::where('user_id', $user)->orderBy('created_at', 'DESC')->get();

        return $this->success($addresses, 'Shipping addresses fetched successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/shipping-address",
     *     summary="Add a new shipping address",
     *     tags={"Ecommerce"},
     *     security={{"bearer": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "address", "city", "area", "pincode", "mobile_number"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="area", type="string"),
     *             @OA\Property(property="pincode", type="string"),
     *             @OA\Property(property="mobile_number", type="string"),
     *             @OA\Property(property="default", type="boolean", description="Set as default address (true/false)")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Address added successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'area' => 'required|string',
            'pincode' => 'required|string',
            'mobile_number' => 'required|string',
            'default' => 'boolean'
        ]);

        $user = auth()->id();

        // If setting as default, reset other addresses
        if ($request->default) {
            ShippingAddress::where('user_id', $user)->update(['default' => false]);
        }

        $address = ShippingAddress::create([
            'user_id' => $user,
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'area' => $request->area,
            'pincode' => $request->pincode,
            'mobile_number' => $request->mobile_number,
            'default' => $request->default ?? false
        ]);

        return $this->success($address, 'Shipping address added successfully', Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/shipping-address/{id}",
     *     summary="Update a shipping address",
     *     tags={"Ecommerce"},
     *     security={{"bearer": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "address", "city", "area", "pincode", "mobile_number"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="area", type="string"),
     *             @OA\Property(property="pincode", type="string"),
     *             @OA\Property(property="mobile_number", type="string"),
     *             @OA\Property(property="default", type="boolean", description="Set as default address (true/false)")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Address updated successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Address not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = Auth::id();

        $address = ShippingAddress::where('id', $id)->where('user_id', $user)->firstOrFail();

        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'area' => 'required|string',
            'pincode' => 'required|string',
            'mobile_number' => 'required|string',
            'default' => 'boolean'
        ]);

        // If setting as default, reset other addresses
        if ($request->default) {
            ShippingAddress::where('user_id', $user)->update(['default' => false]);
        }

        $address->update($request->all());

        return $this->success($address, 'Shipping address updated successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/shipping-address/{id}",
     *     summary="Delete a shipping address",
     *     tags={"Ecommerce"},
     *     security={{"bearer": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Address deleted successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Address not found")
     * )
     */
    public function destroy($id)
    {
        $user = Auth::id();

        $address = ShippingAddress::where('id', $id)->where('user_id', $user)->firstOrFail();
        $address->delete();

        return $this->success([], 'Shipping address deleted successfully', Response::HTTP_OK);
    }
}
