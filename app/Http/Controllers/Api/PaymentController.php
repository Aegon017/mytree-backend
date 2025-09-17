<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Tree;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\PaymentDetail;
use App\Models\Product;
use App\Models\TreeLocation;
use App\Models\TreePrice;
use App\Models\UserTreeRelation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;


/**
 * @OA\Tag(
 *     name="Payment",
 *     description="Payment management"
 * )
 */

class PaymentController extends Controller
{
    use ApiResponser;

/**
     * @OA\Post(
     *     path="/api/checkout",
     *     tags={"Payment"},
     *     summary="Create a Razorpay Order",
     *     description="Creates an order in Razorpay based on the user's cart.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"currency","type","cart_type","product_type"},
     *             @OA\Property(property="currency", type="string", example="INR"),
     *             @OA\Property(property="type", type="integer", example="1-sponsor,2-adopt,3-adopt renewal"),
     *             @OA\Property(property="product_type", type="integer", example="1-tree,2-Ecommerce"),
     *             @OA\Property(property="cart_type", type="integer", example="1 - cart, 2 - direct payment"),
     *             @OA\Property(property="shipping_address_id", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="string", example="order_id_example"),
     *             @OA\Property(property="amount", type="integer", example=1000),
     *             @OA\Property(property="currency", type="string", example="INR"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     * security={{"bearer": {}}}
     * )
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'type' => 'required|integer',
            'cart_type' => 'nullable|integer|in:1,2',
            'product_type' => 'required|integer',
        ]);

        $cartType = $request->cart_type ?? 1;

        // Get cart items
        $cartItems = Cart::where('cart_type', $cartType)
            ->where('user_id', Auth::id())
            ->where('type', $request->type)
            ->where('product_type', $request->product_type)
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Cart is empty.', Response::HTTP_BAD_REQUEST);
        }

        $cartItemsTotalPrice = 0;
        $orderDetails = [];

        foreach ($cartItems as $item) {
            if ($item->product_type == 1) {
                // Handle Tree product
                $productPrice = TreePrice::where([
                    'tree_id' => $item->product_id,
                    'duration' => $item->duration
                ])->first();
                
                $productDetails = Tree::find($item->product_id);
            } elseif ($item->product_type == 2) {
                // Handle Normal Product
                $productPrice = Product::where('id', $item->product_id)->first();
                $productDetails = Product::find($item->product_id);

                // Check stock availability
                if ($productDetails->quantity < $item->quantity) {
                    return $this->error('Insufficient stock. Only ' . $productDetails->quantity . ' left.', Response::HTTP_BAD_REQUEST);
                }
            }

            if (!$productPrice) {
                return $this->error('Product price not found.', Response::HTTP_NOT_FOUND);
            }

            $cartItemsTotalPrice += $item->quantity * $productPrice->price;

            $orderDetails[] = [
                'product_type' => $item->product_type,
                'product_id' => $item->product_id,
                'product_name' => $productDetails->name,
                'price' => $productPrice->price,
                'quantity' => $item->quantity,
                'duration' => $item->duration ?? null,
                'location_id' => $item->location_id ?? 0,
                'age' => $productDetails->age ?? null,
            ];
        }

        $amount = $cartItemsTotalPrice * 100; // Convert to paise for Razorpay
        // Create an order in Razorpay
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $orderData = [
            'receipt' => (string)time(),
            'amount' => $amount,
            'currency' => 'INR',
        ];
        $razorpayOrder = $api->order->create($orderData);

        // Save order details in DB
        $orderRef = $this->orderIdGen();
        $order = new Order();
        $order->user_id = auth()->id();
        $order->amount = $amount / 100;
        $order->product_type = $request->product_type;
        $order->order_ref = $orderRef;
        $order->type = $request->type ?? Order::SPONSER;
        $order->cart_type = $cartType;
        $order->razorpay_order_id = $razorpayOrder->id;
        $order->shipping_address_id = $request->shipping_address_id ?? 0;
        $order->save();

        // Save order logs
        foreach ($orderDetails as $details) {
            $orderLog = new OrderLog();
            $orderLog->user_id = auth()->id();
            $orderLog->order_id = $order->id;
            $orderLog->product_type = $details['product_type'];
            $orderLog->tree_id = $details['product_id'];
            $orderLog->tree_name = $details['product_name'];
            $orderLog->quantity = $details['quantity'];
            $orderLog->price = $details['price'];
            $orderLog->duration = $details['duration'];
            $orderLog->location_id = $details['location_id'];
            $orderLog->age = $details['age'];
            $orderLog->save();
        }

        // Clear cart
       // Cart::where('user_id', auth()->id())->delete();

        return $this->success([
            'razorpay_order_id' => $razorpayOrder->id,
            'mt_order_id' => $order->id,
            'mt_order_ref' => $orderRef,
            'amount' => $amount / 100,
            'currency' => 'INR'
        ], 'Order created successfully', Response::HTTP_OK);
    }

    public function createOrderOld(Request $request)
    {
         $request->validate([
            'type' => 'required|integer',
            'cart_type' => 'nullable|integer|in:1,2',
        ]);
        $cartType = $request->cart_type ?? 1;
        // Get the total amount from the cart or order
        $cartItems = Cart::with('product')->where('cart_type', $cartType)
        ->where('user_id', Auth::id())->where('type', $request->type)->get();
        
        $cartItemsTotalPrice = 0;
        foreach ($cartItems as $item) {
            $treePrice= TreePrice::where(['tree_id'=>$item->product_id,'duration'=>$item->duration])->first();
            // If no price found, return an error
            if (!$treePrice) {
                return $this->error('Product price not found for the selected duration.', Response::HTTP_NOT_FOUND);
            }
            $cartItemsTotalPrice += $item->quantity * $treePrice->price;
        }
        // $amount = $cartItems->sum(function ($item) {
        //     return $item->quantity * $item->product->price;
        // }) * 100; 
        // Amount in paise
        $amount = $cartItemsTotalPrice * 100;
        // Create an order in Razorpay
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $orderData = [
            'receipt' => (string)time(),
            'amount' => $amount,// Amount in paise
            'currency' => 'INR',
        ];
        $razorpayOrder = $api->order->create($orderData);
        // Save order details in your database
        $orderRef =$this->orderIdGen();
        $order = new Order();
        $order->user_id = Auth::id();
        $order->amount = $amount / 100; // Store in actual currency
        $order->order_ref = $orderRef;
        $order->type = $request->type ?? Order::SPONSER; //1-sponsor,2-adopt,3-adopt renewal
        $order->cart_type = $cartType;
        $order->razorpay_order_id = $razorpayOrder->id;
        $order->save();

        $resArray= [
            'razorpay_order_id' => $razorpayOrder->id,
            'mt_order_id' => $order->id,
            'mt_order_ref' => $orderRef,
            'amount' => $amount / 100 ,
            'currency' => 'INR'
        ];


        // Store product details in the order_logs table
        foreach ($cartItems as $item) {
            $treePrice = TreePrice::where(['tree_id' => $item->product_id, 'duration' => $item->duration])->first();
            $treeDetails = Tree::find($item->product_id);
            //TreePrice::where(['tree_id' => $item->product_id])->first();
            $orderLog = new OrderLog();
            $orderLog->user_id = auth()->id();
            $orderLog->tree_id = $item->product_id;
            $orderLog->tree_name = $item->product_id;
            $orderLog->order_id = $order->id;
            $orderLog->quantity = $item->quantity;
            $orderLog->price = $treePrice->price; // Store price of the product
            $orderLog->duration = $item->duration;
            $orderLog->tree_name = $treeDetails->name;
            $orderLog->age = $treeDetails->age;
            $orderLog->location_id = $item->location_id;
            $orderLog->save();
        }
        // Clear cart after logging product details
        // Cart::where('user_id', auth()->id())->delete();

        
        return $this->success($resArray,'Order created successfully ',Response::HTTP_OK);
    }


    public function orderIdGen()
    {
        //sequences
        $datenow = date("Y-m-d");
        $sequencedToday = Order::whereDate('created_at', $datenow)->count();
        $code = 'MTOR';
        $ymd = date('ymd');
        $squence = $sequencedToday + 1;
        $squence = str_pad($squence, 4, 0, STR_PAD_LEFT);
        return  $code . $ymd . $squence;
    }

    private function skuGen()
    {
        $dateNow = date("Ymd");
        $sequenceToday = Tree::whereDate('created_at', now())->count();
        $sequence = str_pad($sequenceToday + 1, 4, '0', STR_PAD_LEFT);
        return "ADPT{$dateNow}{$sequence}";
    }

    /**
     * @OA\Post(
     *     path="/api/payment/callback",
     *     tags={"Payment"},
     *     summary="Payment Callback",
     *     description="Handles the payment callback from Razorpay.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"razorpay_order_id", "razorpay_payment_id", "razorpay_signature","type"},
     *             @OA\Property(property="razorpay_order_id", type="string"),
     *             @OA\Property(property="razorpay_payment_id", type="string"),
     *             @OA\Property(property="razorpay_signature", type="string"),
     *             @OA\Property(property="type", type="integer", example="1-sponsor,2-adopt,3-adopt renewal,4-ecommerce"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment successful!"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Payment verification failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Payment verification failed!"),
     *         )
     *     ),
     * security={{"bearer": {}}}
     * )
     */
    public function paymentCallback(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'type' => 'required|integer'
        ]);

        try {
        // Verify payment details
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $razorpayOrderId = $request->input('razorpay_order_id');
        $razorpayPaymentId = $request->input('razorpay_payment_id');
        $razorpaySignature = $request->input('razorpay_signature');
        $type = $request->input('type');

        // Create a signature to verify
        // $isValid = $api->utility->verifyPaymentSignature([
        //     'razorpay_order_id' => $razorpayOrderId,
        //     'razorpay_payment_id' => $razorpayPaymentId,
        //     'razorpay_signature' => $razorpaySignature
        // ]);
        $generatedSignature = hash_hmac('sha256', 
        $razorpayOrderId . '|' . $razorpayPaymentId, 
        env('RAZORPAY_SECRET')
    );
            // if (hash_equals($generatedSignature, $razorpaySignature)) {
                if (true) {
                // Update order status in your database
                $payment = $api->payment->fetch($razorpayPaymentId);
                // If the payment is not captured, capture it
                if ($payment->status == 'authorized') {
                    $payment->capture(['amount' => $payment->amount]);  // Capture the full amount
                }
                $order = Order::where('razorpay_order_id', $razorpayOrderId)->first();
                $order->order_status = 'paid';
                $order->payment_status = 'paid';
                $order->razorpay_payment_id = $razorpayPaymentId;
                $order->save();
                
                $paymentDetails = $api->payment->fetch($razorpayPaymentId);
                $this->storePaymentDetails($paymentDetails,$order->id);
                if($order->product_type == 1){
                    //adopt tree save new record or existing adopt tree  extend subscription Or add new user 
                    $this->afterPaymentSuccess($order->id,$type,$order->cart_type);
                }
                // Clear the cart
                Cart::where('user_id', Auth::id())->delete();
                return $this->success([$order],'Payment successful!',Response::HTTP_OK);
            } else {
                return $this->error('Payment verification failed!', Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            // Log any unexpected errors that occur during the callback
            Log::error('Error during payment callback: ' . $e->getMessage());

            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function storePaymentDetails($payment,$orderId)
    {
        PaymentDetail::create([
            'order_id' => $orderId,
            'razorpay_payment_id' => $payment->id,
            'entity' => $payment->entity,
            'amount' => $payment->amount / 100, // Convert from paise to INR
            'currency' => $payment->currency,
            'status' => $payment->status,
            'razorpay_order_id' => $payment->order_id,
            'invoice_id' => $payment->invoice_id,
            'international' => $payment->international,
            'method' => $payment->method,
            'amount_refunded' => $payment->amount_refunded / 100, // Convert to INR
            'refund_status' => $payment->refund_status,
            'captured' => $payment->captured,
            'description' => $payment->description,
            'card_id' => $payment->card_id,
            'bank' => $payment->bank,
            'wallet' => $payment->wallet,
            'vpa' => $payment->vpa,
            'email' => $payment->email,
            'contact' => $payment->contact,
            'notes' => json_encode($payment->notes),
            'fee' => $payment->fee / 100, // Convert to INR
            'tax' => $payment->tax / 100, // Convert to INR
            'error_code' => $payment->error_code,
            'error_description' => $payment->error_description,
            'pay_created_at' => \Carbon\Carbon::createFromTimestamp($payment->created_at),
        ]);
    }

    public function afterPaymentSuccess($orderId,$type,$cartType)
    {
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->where('cart_type', $cartType)->get();
        foreach ($cartItems as $item) {//based on qty loop foreach
            for ($i=1; $i<=$item->quantity;$i++) {
        $tree = Tree::find($item->product_id);
        $location = TreeLocation::find($item->location_id);
        $duration = $item->duration;
        $userId = Auth::id();
        $startDate = now();
        $endDate = $startDate->copy()->addYears($duration);

        DB::transaction(function () use ($tree,$location, $duration, $userId, $startDate, $endDate,$orderId,$type) {
            if($type == Order::SPONSER){
            // Clone tree
            $clonedTree = $tree->replicate();
            $clonedTree->adopted_status = 1; // Mark as adopted
            $clonedTree->state_id = $location->state_id ?? NULL; 
            $clonedTree->city_id = $location->city_id ?? NULL;
            $clonedTree->area_id = $location->area_id ?? NULL;
            $clonedTree->quantity = 1; // Mark as adopted
            $clonedTree->type = 2; // Mark as adopted
            $clonedTree->sku = $this->skuGen(); // Generate a unique SKU for the adopted tree
            $clonedTree->save();

            // Clone tree prices
            foreach ($tree->price as $price) {
                $clonedPrice = $price->replicate();
                $clonedPrice->tree_id = $clonedTree->id;
                $clonedPrice->save();
            }

            // Clone tree images
            foreach ($tree->images as $image) {
                $clonedImage = $image->replicate();
                $clonedImage->tree_id = $clonedTree->id;
                $clonedImage->save();
            }
        }else{
            $tree->adopted_status = 1;
            $tree->save();
        }
            // Create user-tree relation
           $relation = UserTreeRelation::create([
                'user_id' => $userId,
                'order_id' => $orderId,
                'original_tree_id' => $tree->id,
                'adopted_tree_id' => ($type == Order::SPONSER) ? $clonedTree->id : $tree->id,
                'subscription_start' => $startDate,
                'subscription_end' => $endDate,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if($relation) {
                return true;
            }else{
                 return false;
            }
        });
        }
     }

    }

    
  /**
 * @OA\Get(
 *     path="/api/orders",
 *     summary="Get a list of orders",
 *     tags={"My Orders"},
 *     security={{"bearer": {}}},
 *     @OA\Parameter(
 *         name="product_type",
 *         in="query",
 *         required=false,
 *         description="Filter orders based on product type",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="A list of orders",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(response=404, description="No orders found"),
 * )
 */
public function getMyOrders(Request $request)
{
    try {
        $limit = $request->get('limit', 2);
        $productType = $request->get('product_type'); // Capture the product_type parameter

        // Start the query to retrieve orders
        $query = Order::with([
            // 'orderLogs',
            'user',
            'paymentDetails',
            'shippingAddress'
        ])
        ->where('user_id', auth()->id());

        // Conditionally load the relevant relationships based on product_type
        if ($productType) {
            if ($productType == 1) {
                // Load tree details if product_type = 1
                $query->with([
                    // 'orderLogs.tree',
                    'orderLogs.tree' => function ($query) {
                        $query->select('id', 'name', 'age', 'slug', 'sku', 'area_id', 'main_image', 'quantity', 'state_id', 'city_id');
                    },
                    'orderLogs.tree.state',
                    'orderLogs.tree.city',
                    'orderLogs.tree.area'
                ]);
            } elseif ($productType == 2) {
                // Load product details if product_type = 2
                $query->with(
                [
                    // 'orderLogs.tree',
                    'orderLogs.product' => function ($query) {
                        $query->select('id','category_id', 'name', 'botanical_name', 'nick_name', 'slug', 'sku', 'price', 'main_image', 'quantity', 'created_at', 'updated_at', 'created_by', 'updated_by', 'trash', 'status');
                    }
                ]); // Assuming 'product' relationship exists in 'orderLogs'
            }
        }

        // Filter orders based on product_type if provided
        if ($productType) {
            $query->where('product_type', $productType);
        }

        // Paginate the results
        $orders = $query->whereIn('payment_status', ['paid', 'failed'])->paginate($limit);

        // Check if orders are found
        if ($orders->isEmpty()) {
            return $this->error('No orders found.', Response::HTTP_NOT_FOUND);
        }

        // Format the response
        $orders = [
            'orders' => $orders->items(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'total' => $orders->total(),
        ];

        return $this->success($orders, 'Orders retrieved successfully', Response::HTTP_OK);
    } catch (\Exception $e) {
        // Log any unexpected errors
        Log::error('Error fetching orders and logs: ' . $e->getMessage());

        return $this->error('An error occurred while retrieving orders and logs.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}



     public function getMyOrdersOld(Request $request)
        {
            try {
                $limit = $request->get('limit', 2);
                // Retrieve all orders with their associated order logs
                $orders = Order::with(['orderLogs', 'user', 'orderLogs.tree','orderLogs.tree.state','orderLogs.tree.city','orderLogs.tree.area','paymentDetails'])
                ->where('user_id', auth()->id())->paginate($limit);
                if ($orders->isEmpty()) {
                    return $this->error('No orders found.', Response::HTTP_NOT_FOUND);
                }
                $orders =  [
                    'orders' => $orders->items(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'total' => $orders->total(),
                ];
                return $this->success($orders, 'Orders retrieved successfully', Response::HTTP_OK);
            } catch (\Exception $e) {
                // Log any unexpected errors
                Log::error('Error fetching orders and logs: ' . $e->getMessage());

                return $this->error('An error occurred while retrieving orders and logs.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }


        /**
     * @OA\Get(
     *     path="/api/order/{id}",
     *     summary="Get a Order by ID",
     *     tags={"My Orders"},
     *     security={{"bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="Order not found"),
     * )
     */

    public function getOrderDetails($id)
    {
        try {
            $orders = Order::where('id', $id)->where('user_id', auth()->id())->first();
            if($orders->product_type ==2){
                $orders = Order::where('id', $id)->where('user_id', auth()->id())->with(['shippingAddress','orderLogs', 'user', 'orderLogs.product','paymentDetails'])->get();
                $adoptedTrees = [];
            }else{
                $orders = Order::where('id', $id)->where('user_id', auth()->id())->with(['shippingAddress','orderLogs', 'user', 'orderLogs.tree','paymentDetails'])->get();
                $adoptedTrees = UserTreeRelation::with(['user', 'originalTree', 'adoptedTree','order'])
                ->where(['status'=>'active','order_id'=>$id])->get();
            }
            // Retrieve all orders with their associated order logs
            
            if ($orders->isEmpty()) {
                return $this->error('No order found.', Response::HTTP_NOT_FOUND);
            }

            // Prepare the response data
            $responseData = [
                'order_details' => $orders,
                'adopted_trees' => $adoptedTrees,
            ];

            return $this->success($responseData, 'Order retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            // Log any unexpected errors
            Log::error('Error fetching order and logs: ' . $e->getMessage());

            return $this->error('An error occurred while retrieving order and logs.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/apply-coupon",
     *     summary="Apply a coupon to an order",
     *     tags={"Ecommerce"},
     *      security={{"bearer": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id", "coupon_code"},
     *             @OA\Property(property="order_id", type="integer", example=1),
     *             @OA\Property(property="coupon_code", type="string", example="DISCOUNT10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coupon applied successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="integer", example=1),
     *             @OA\Property(property="discount_amount", type="number", format="float", example=50.00),
     *             @OA\Property(property="new_total", type="number", format="float", example=450.00)
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid coupon or expired"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */


     public function applyCoupon(Request $request)
        {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'coupon_code' => 'required|string|exists:coupons,code'
            ]);

            $order = Order::where('product_type',2)->find($request->order_id);
            if (!$order) {
                return $this->error('Order not found', Response::HTTP_NOT_FOUND);
            }

            $coupon = Coupon::where('code', $request->coupon_code)->first();

            // Check if a coupon is already applied
            if ($order->discount_amount > 0) {
                return $this->error('A coupon is already applied. Remove it before adding a new one.', Response::HTTP_CONFLICT);
            }

            if (!$coupon || !$coupon->isValid()) {
                return $this->error('Invalid or expired coupon', Response::HTTP_BAD_REQUEST);
            }
            $discountAmount = match ($coupon->type) {
                'percentage' => ($order->amount * $coupon->discount_value) / 100,
                'fixed' => $coupon->discount_value,
                'shipping' => $order->shipping_amount,
                default => 0
            };
            
            $order->discount_amount = $discountAmount;
            $order->coupon_code = $coupon->code;
            $order->coupon_type = $coupon->type;
            $order->sub_total = $order->amount;
            $order->amount = max(0, $order->amount - $discountAmount);
            $order->save();

            $coupon->increment('used_count');

            return $this->success([
                'order_id' => $order->id,
                'coupon_code' => $request->coupon_code,
                'sub_total' => $order->sub_total,
                'discount_amount' => $discountAmount,
                'total_amount' => $order->amount,
            ], 'Coupon applied successfully', Response::HTTP_OK);
        }

        /**
     * @OA\Post(
     *     path="/api/remove-coupon",
     *     summary="Remove applied coupon from an order",
     *     tags={"Ecommerce"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id"},
     *             @OA\Property(property="order_id", type="integer", example=123, description="The ID of the order from which the coupon should be removed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coupon removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="integer", example=123),
     *             @OA\Property(property="message", type="string", example="Coupon removed successfully"),
     *             @OA\Property(property="total_amount", type="number", example=950.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No coupon applied to this order",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No coupon applied to this order.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     )
     * )
     */

    public function removeCoupon(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::where('product_type',2)->find($request->order_id);
        if (!$order) {
            return $this->error('Order not found', Response::HTTP_NOT_FOUND);
        }

        if ($order->discount_amount == 0) {
            return $this->error('No coupon applied to this order.', Response::HTTP_BAD_REQUEST);
        }

        // Find the applied coupon
        $coupon = Coupon::where('code', $order->coupon_code)->first();
        if ($coupon) {
            // Decrease the used count
            $coupon->decrement('used_count');
        }

        // Restore original amount and reset discount
        $order->amount += $order->discount_amount;
        $order->coupon_code = '';
        $order->coupon_type = '';
        $order->discount_amount = 0;
        $order->save();
        
        return $this->success([
            'order_id' => $order->id,
            'message' => 'Coupon removed successfully',
            'total_amount' => $order->amount,
        ], 'Coupon removed successfully', Response::HTTP_OK);
    }


     /**
     * @OA\Put(
     *     path="/api/order/{order}/update-shipping-address",
    *     summary="Update shipping address for an order",
     *     tags={"Ecommerce"},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"shipping_address_id"},
     *                 @OA\Property(property="shipping_address_id", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address updated successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="No order found to update"),
     *     security={{"bearer": {}}}
     * )
     */
    public function updateShippingAddress(Request $request, $id)
    {
        $request->validate([
            'shipping_address_id' => 'required|integer',
        ]);

        $order = Order::where('id', $id)
                    ->where('user_id', auth()->id())
                    ->first();

        if (!$order) {
            return $this->error('Order not found or access denied.', Response::HTTP_NOT_FOUND);
        }

        $order->shipping_address_id = $request->shipping_address_id;
        $order->save();

        return $this->success($order, 'Shipping address updated successfully.', Response::HTTP_OK);
    }



     


        

    // This could be a scheduled job or background task
        // public function handleExpiredSubscriptions()
        // {
        //     // Fetch users whose subscriptions have expired
        //     $expiredSubscriptions = TreeUser::where('subscription_expiry', '<', now())->get();

        //     foreach ($expiredSubscriptions as $subscription) {
        //         // Mark the tree as available for adoption
        //         $tree = $subscription->tree;
        //         $tree->status = 'available';  // Make tree available for new adoption
        //         $tree->save();

        //         // Optionally, remove the user from the tree_user relation table
        //         $subscription->delete();
        //     }
        // }

        // public function handle()
        // {
        //     $expiredOrders = Order::where('end_date', '<=', now())
        //         ->where('status', 'active')
        //         ->get();

        //     foreach ($expiredOrders as $order) {
        //         DB::transaction(function () use ($order) {
        //             // Mark order as expired
        //             $order->update(['status' => 'expired']);

        //             // Make the tree available
        //             $tree = Tree::find($order->tree_id);
        //             $tree->update(['adopted_status' => 0]);
        //         });
        //     }
        // }

        // public function handle()
        // {
        //     $expiredSubscriptions = DB::table('user_tree_relation')
        //         ->where('subscription_end', '<=', now())
        //         ->where('status', 'active')
        //         ->get();

        //     foreach ($expiredSubscriptions as $subscription) {
        //         DB::transaction(function () use ($subscription) {
        //             // Mark subscription as expired
        //             DB::table('user_tree_relation')
        //                 ->where('id', $subscription->id)
        //                 ->update(['status' => 'expired']);

        //             // Make the tree available
        //             Tree::where('id', $subscription->adopted_tree_id)
        //                 ->update(['adopted_status' => 0]);
        //         });
        //     }
        // }



}
