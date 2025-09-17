<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CartDetailsRequest;
use App\Models\Admin\Tree;
use App\Models\Cart;
use App\Models\Product;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Coupon;

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="Cart management"
 * )
 */

class CartController extends Controller
{
    use ApiResponser;


    /**
 * @OA\Post(
 *     path="/api/cart/add/{productId}",
 *     tags={"Cart"},
 *     summary="Add a product to the cart",
 *     description="Adds a product to the user's cart, or updates the quantity if it already exists.",
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         required=true,
 *         description="ID of the product to add",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quantity","type","product_type","duration","location_id"},
 *             @OA\Property(property="quantity", type="integer", example=1),
 *             @OA\Property(property="type", type="integer", example="1-sponsor,2-adopt,3-adopt renewal"),
 *             @OA\Property(property="product_type", type="integer", example="1-tree,2-ecommerce"),
 *             @OA\Property(property="duration", type="integer", example=1, description="Duration in years (1, 2, 3) or other units depending on your business logic"),
 *             @OA\Property(property="name", type="string", example="Harish Mogilipuri"),
 *             @OA\Property(property="occasion", type="string", example="Birthday"),
 *             @OA\Property(property="message", type="string", example="Happy Birthday!"),
 *             @OA\Property(property="location_id", type="integer", example=1),
 *             @OA\Property(property="cart_type", type="integer", example="1 - cart, 2 - direct payment"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product added to cart",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="boolean")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *     ),
 *     security={{"bearer": {}}}
 * )
 */
    public function addCart(Request $request, $productId)
    {
        // Define the validation rules
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'duration' => 'nullable|integer',
            'type' => 'required|integer',
            'product_type' => 'required|integer',
            'location_id' => 'nullable|integer',
            'cart_type' => 'nullable|integer|in:1,2',
        ]);

        // If validation fails, return custom error response
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all(); // Get all errors as an array
            $errorString = implode(', ', $errorMessages); // Join errors with a comma
            $response = [
                'code' => 422,
                'status' => 'errors',
                'data' => [],
                'message' => $errorString,
            ];
            return response()->json($response, $response['code']);
        }

        // dd($request->product_type);
        $product = ($request->product_type == 1 ? Tree::find($productId) : Product::find($productId) );
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }
        //Tree::findOrFail($productId);
        $userId = auth()->id();
        $cartType = $request->cart_type ?? 1;
        if($cartType == 2){ // if it's direct payment then only accept one product to pay
            Cart::where('user_id', Auth::id())->where('cart_type', $cartType)->delete();
        }

        // // // Ensure cart has only one product type
        // $existingCart = Cart::where('user_id', $userId)->where('product_type', $request->product_type)->first();
        // if ($existingCart && $existingCart->type !== $request->type) {
        //     Cart::where('user_id', $userId)->where('cart_type', $cartType)->delete();
        // }

        // Get the type of the product you're adding (assuming it's a property of the product)
        $productType = $product->type;  // Modify this as needed based on your model
        // Get the existing cart for the current user and the same product
        $cart = Cart::where('product_id', $productId)
                    ->where('user_id', $userId)
                    ->where('type', $request->type)
                    ->where('cart_type', $cartType)
                    ->where('product_type', $request->product_type)
                    ->first();

        // Check if there are other products with a different type in the cart
        $existingCartWithDifferentType = Cart::where('user_id', $userId)
                                            ->where('type', '!=', $productType)
                                            ->exists();
        if ($cart) {
            // Check if the type of the new product matches the type of the existing cart
            if ($cart->type !== $productType && $existingCartWithDifferentType) {
                return $this->error('You can only add products of the same type. Please clear the cart or add products of the same type.', Response::HTTP_BAD_REQUEST);
            }

            // Update quantity and other details
            // $cart->quantity += $request->quantity;
            // If quantity in the request is greater than the existing quantity, increment it
            if($request->product_type == 2){
                 // Check stock availability
                if ($product->quantity < $request->quantity) {
                 return $this->error('Insufficient stock. Only ' . $product->quantity . ' left.', Response::HTTP_BAD_REQUEST);
                }
            }
            if ($request->quantity != $cart->quantity) {
                $cart->quantity = $request->quantity;
            }
            $cart->duration = $request->duration ?? 0;
            $cart->name = $request->name ?? '';
            $cart->occasion = $request->occasion ?? '';
            $cart->message = $request->message ?? '';
            $cart->type = $productType; // Ensure the type is updated
            $cart->product_type = $request->product_type; // Ensure the type is updated
            $cart->location_id = $request->location_id ?? 0;

            $cart->save();
        } else {
            // Check if a cart already exists with a different type
            if ($existingCartWithDifferentType) {
                return $this->error('You can only add products of the same type. Please clear the cart or add products of the same type.', Response::HTTP_BAD_REQUEST);
            }

            // Add new product to the cart
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'duration' => $request->duration ?? 0,
                'name' => $request->name ?? '',
                'occasion' => $request->occasion ?? '',
                'message' => $request->message ?? '',
                'cart_type' => $cartType,
                'type' => $productType, // Set the type of the product
                'product_type' => $request->product_type, // Set the type of the product
                'location_id' => $request->location_id ?? 0
            ]);
        }

        return $this->success($cart, trans('cart.add'), Response::HTTP_OK);
    }



    /**
     * @OA\Get(
     *     path="/api/cart",
     *     tags={"Cart"},
     *     summary="View cart items",
     *     description="Retrieves the items in the user's cart.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of cart items",
     *         @OA\JsonContent(
     *         )
     *     ),
     *     security={{"bearer": {}}}
     * )
     */

     public function viewCart()
    {
        $cartItems = Cart::where('user_id', auth()->id())
            ->where('cart_type', 1)
            ->get()
            ->map(function ($cartItem) {
                if ($cartItem->product_type == 1) {
                    $cartItem->load([
                        'product', 
                        'location.state', 
                        'location.city', 
                        'location.area',
                        'product.price' => function ($query) use ($cartItem) {
                            $query->where('duration', $cartItem->duration);
                        }
                    ]);
                } elseif ($cartItem->product_type == 2) {
                    $cartItem->load(['ecomProduct','ecomProduct.category']);
                }
                return $cartItem;
            });

        return $this->success($cartItems, trans('user.success'), Response::HTTP_OK);
    }

    public function viewCartOld()
    {
        $cartItems = Cart::where('user_id', auth()->id())
        ->where('cart_type', 1)
        ->get()
        ->map(function ($cartItem) {
            if ($cartItem->product_type == 1) {
            // Apply the condition for each cart item
            $cartItem->load(['product','location.state','location.city','location.area', 'product.price' => function ($query) use ($cartItem) {
                $query->where('duration', $cartItem->duration);
            }]);
            
        }elseif ($cartItem->product_type == 2) {
            $cartItem->load('product');
        }
        return $cartItem;
        });
        return $this->success($cartItems,trans('user.success'),Response::HTTP_OK);
    }


        /**
     * @OA\Delete(
     *     path="/api/cart/remove/{cartId}",
     *     tags={"Cart"},
     *     summary="Remove a product from the cart",
     *     description="Removes a product from the user's cart.",
     *     @OA\Parameter(
     *         name="cartId",
     *         in="path",
     *         required=true,
     *         description="ID of the cart item to remove",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product removed from cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart item not found",
     *     ),
     *     security={{"bearer": {}}}
     * )
     */
    public function remove($cartId)
    {
        $cartItem = Cart::where('id', $cartId)
                        ->where('cart_type', 1)
                        ->where('user_id', auth()->id())
                        ->first();

        if (!$cartItem) {
            return $this->error(trans('cart.not_found'), Response::HTTP_NOT_FOUND);
        }

        $cartItem->delete();
        return $this->success($cartItem,trans('cart.removed'),Response::HTTP_OK);
    }

      /**
     * @OA\Get(
     *     path="/api/cart/clear",
     *     tags={"Cart"},
     *     summary="Clear all products from the cart",
     *     description="Clear all products from the user's cart.",
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Product removed from cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found",
     *     ),
     *     security={{"bearer": {}}}
     * )
     */
    public function clearCart()
    {
        $cartItem = Cart::where('user_id', auth()->id())->where('cart_type', 1)
                        ->delete();

        return $this->success($cartItem,trans('cart.clear'),Response::HTTP_OK);
    }

    /**
 * @OA\Post(
 *     path="/api/cart/addDetails/{cartId}",
 *     tags={"Cart"},
 *     summary="Details added",
 *     description="Adds a product to the user's cart, or updates the quantity if it already exists.",
 *     @OA\Parameter(
 *         name="cartId",
 *         in="path",
 *         required=true,
 *         description="ID of the product to add",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={ "name", "occasion", "message"},
 *             @OA\Property(property="name", type="string", example="Harish Mogilipuri"),
 *             @OA\Property(property="occasion", type="string", example="Birthday"),
 *             @OA\Property(property="message", type="string", example="Happy Birthday!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Details added to product",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="boolean")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *     ),
 *     security={{"bearer": {}}}
 * )
 */
public function addCartDetails(CartDetailsRequest $request, $cartId)
{
    // Find the cart by ID
    $cartItem = Cart::find($cartId);
    if (!$cartItem) {
        return $this->error(trans('cart.not_found'), Response::HTTP_NOT_FOUND);
    }
    // Check if the product is already in the cart for the current user
    $cart = Cart::where('id', $cartId)
                ->where('user_id', auth()->id())
                ->first();

    if ($cart) {
        // Update  user_name, occasion, and message if the product is already in the cart
        $cart->name = $request->name;
        $cart->occasion = $request->occasion;
        $cart->message = $request->message;
        $cart->save();
    }
    // Return a success response with the updated cart details
    return $this->success($cart, trans('cart.details'), Response::HTTP_OK);
}
    

/**
 * @OA\Post(
 *     path="/api/cart/apply-coupon-all",
 *     summary="Apply a coupon to all cart items of a user with product_type = 2",
 *     tags={"Ecommerce"},
 *     security={{"bearer":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"coupon_code"},
 *             @OA\Property(property="coupon_code", type="string", example="SAVE20", description="The coupon code to apply to all cart items.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Coupon applied successfully to all cart items",
 *         @OA\JsonContent(
 *             @OA\Property(property="user_id", type="integer", example=123),
 *             @OA\Property(property="coupon_code", type="string", example="SAVE20"),
 *             @OA\Property(property="total_discount_amount", type="number", example=50.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid coupon or no applicable cart items found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid or expired coupon")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found or not authenticated")
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="A coupon is already applied to one or more cart items",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="A coupon is already applied to one or more cart items. Remove it before adding a new one.")
 *         )
 *     )
 * )
 */


 public function applyCouponToAll(Request $request)
{
    $request->validate([
        'coupon_code' => 'required|string|exists:coupons,code'
    ]);

    // Get the current user
    $user = auth()->user();
    
    if (!$user) {
        return $this->error('User not found or not authenticated', Response::HTTP_UNAUTHORIZED);
    }

    // Find the coupon
    $coupon = Coupon::where('code', $request->coupon_code)->first();
    
    if (!$coupon || !$coupon->isValid()) {
        return $this->error('Invalid or expired coupon', Response::HTTP_BAD_REQUEST);
    }

    // Get all cart items of the current user where product_type = 2
    $cartItems = Cart::with('ecomProduct')->where('user_id', $user->id)
                    ->where('product_type', 2)  // Product type should be 2
                    ->get();

    if ($cartItems->isEmpty()) {
        return $this->error('No applicable cart items found for this user.', Response::HTTP_BAD_REQUEST);
    }

    // Calculate the total value of all cart items
    $totalCartValue = $cartItems->sum(function ($cartItem) {
        return $cartItem->ecomProduct->price * $cartItem->quantity; // Assuming `quantity` is a field in Cart
    });

    // Calculate the total discount based on coupon type
    $totalDiscountAmount = 0;
    $discountAmount = 0;

    switch ($coupon->type) {
        case 'percentage':
            $totalDiscountAmount = ($totalCartValue * $coupon->discount_value) / 100;
            break;
        case 'fixed':
            $totalDiscountAmount = $coupon->discount_value;
            break;
        case 'shipping':
            // Assuming the shipping cost applies to the total cart value
            $totalDiscountAmount = $cartItems->sum(function ($cartItem) {
                return $cartItem->ecomProduct->shipping_cost; // Assuming `shipping_cost` is a property of ecomProduct
            });
            break;
        default:
            $totalDiscountAmount = 0;
            break;
    }

    // Apply the total discount to each cart item and update
    foreach ($cartItems as $cartItem) {
        // Calculate the individual discount for this item based on the total cart value
        // $itemPercentageDiscount = ($cartItem->ecomProduct->price * $cartItem->quantity) / $totalCartValue;
        // $itemDiscountAmount = $totalDiscountAmount * $itemPercentageDiscount;

        // Apply the discount to the cart item
        // $cartItem->discount_amount = $itemDiscountAmount;
        // $cartItem->total_price = ($cartItem->ecomProduct->price * $cartItem->quantity) - $itemDiscountAmount;
        $cartItem->coupon_code = $coupon->code;
        // $cartItem->coupon_type = $coupon->type;
        $cartItem->save();
    }

    // Increment the coupon's usage count
    // $coupon->increment('used_count');

    return $this->success([
        'user_id' => $user->id,
        'coupon_code' => $coupon->code,
        'total_amount' => $totalCartValue,
        'total_discount_amount' => $totalDiscountAmount,
    ], 'Coupon applied successfully to all cart items.', Response::HTTP_OK);
}
    // public function checkout()
    // {
    //     $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();

    //     // Check if cart is empty
    //     if ($cartItems->isEmpty()) {
    //         return $this->error(trans('cart.empty'), Response::HTTP_NOT_FOUND);
    //     }
    //     return $this->success($cartItems,'success',Response::HTTP_OK);
    // }

    // public function placeOrder(Request $request)
    // {
    //     // Logic to process payment and create an order

    //     // Clear cart after successful order
    //     $cartItems = Cart::where('user_id', auth()->id())->delete();
    //     return $this->success($cartItems,'Order placed successfully!',Response::HTTP_OK);
    // }
}
