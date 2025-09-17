<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\DonationTransaction;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

/**
 * @OA\Tag(name="Feed Tree", description="Operations related to Feed Tree")
 */

class DonationController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Post(
     *     path="/api/feed-tree/{campaignId}/donation/initiate",
     *     summary="Initiate donation payment via Razorpay",
     *     tags={"Donations"},
     *     security={{"bearer": {}}},
     *     @OA\Parameter(
     *         name="campaignId",
     *         in="path",
     *         required=true,
     *         description="Campaign ID for the donation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", example=500.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment order successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="string", example="order_DjzPzU1A6Xjjtb"),
     *             @OA\Property(property="amount", type="integer", example=50000),
     *             @OA\Property(property="currency", type="string", example="INR"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request parameters"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Campaign not found"
     *     )
     * )
     */
    public function initiatePayment(Request $request, $campaignId)
    {
        $user = Auth::user();
        // Validate incoming request
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Get the campaign to ensure it's valid
        $campaign = Campaign::find($campaignId);
        if(!$campaign){
            return $this->error('Campaign not found', 404);
        }
        // Create Razorpay Order
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        // dd($api);
        $orderData = [
            'amount' => $request->amount * 100, // Razorpay expects amount in paise
            'currency' => 'INR',
            'receipt' => (string)time(), // Optional receipt identifier
            'notes' => [
                'campaign_id' => $campaignId,
                'donor_name' => $user->name ?? 'NA',
                'donor_email' => $user->email ?? 'NA',
            ]
        ];

        // Create order via Razorpay API
        $razorpayOrder = $api->order->create($orderData);
        $donorOrderId = $this->orderIdGen();
        $resArray =[
            'donation_order_id' => $donorOrderId,
            'razorpay_order_id' => $razorpayOrder->id,
            'amount' => $razorpayOrder->amount,
            'currency' => $razorpayOrder->currency,
        ];

        DonationTransaction::create([
            'campaign_id' => $campaignId,
            'donation_order_id' => $donorOrderId,
            'donor_id' => $user->id,
            'amount' => $razorpayOrder->amount/100,
            'razorpay_order_id' => $razorpayOrder->id,
            'status' => 'pending', // Payment not completed yet
            'notes' => json_encode($orderData['notes']),
        ]);
        // Send the payment details back to the client (to initiate payment)
        return $this->success($resArray,'Order created successfully ',Response::HTTP_OK);
    }

    public function orderIdGen()
    {
        //sequences
        $datenow = date("Y-m-d");
        $sequencedToday = DonationTransaction::whereDate('created_at', $datenow)->count();
        $code = 'DONOR';
        $ymd = date('ymd');
        $squence = $sequencedToday + 1;
        $squence = str_pad($squence, 4, 0, STR_PAD_LEFT);
        return  $code . $ymd . $squence;
    }

    /**
     * @OA\Post(
     *     path="/api/feed-tree/donation-payment/callback",
     *     summary="Payment callback for verifying Razorpay payment",
     *     tags={"Donations"},
     *     security={{"bearer": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"razorpay_order_id", "razorpay_payment_id", "razorpay_signature","donation_order_id"},
     *             @OA\Property(property="donation_order_id", type="string", example="DONOR2412180005"),
     *             @OA\Property(property="razorpay_order_id", type="string", example="order_DjzPzU1A6Xjjtb"),
     *             @OA\Property(property="razorpay_payment_id", type="string", example="pay_CpXjmfXZ8FgopV"),
     *             @OA\Property(property="razorpay_signature", type="string", example="9d8b8a7d57d897a09ff3bfa6be82840bbd597f9834cb649b8f7a64b42a9cf70b5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Donation successfully processed and payment verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Donation successfully processed and verified.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid payment verification or missing data"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */

    public function paymentCallback(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'donation_order_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);
        // Extract payment details from the callback request
        $donationOrderId = $request->input('donation_order_id');
        $razorpayOrderId = $request->input('razorpay_order_id');
        $razorpayPaymentId = $request->input('razorpay_payment_id');
        $razorpaySignature = $request->input('razorpay_signature');

        // Verify payment signature via Razorpay API
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            // $isValid = $api->utility->verifyPaymentSignature([
            //     'razorpay_order_id' => $razorpayOrderId,
            //     'razorpay_payment_id' => $razorpayPaymentId,
            //     'razorpay_signature' => $razorpaySignature
            // ]);

            $transaction = DonationTransaction::where('razorpay_order_id', $razorpayOrderId)->first();
            
            if (!$transaction) {
                return $this->error('Transaction not found!', Response::HTTP_NOT_FOUND);
            }

            $generatedSignature = hash_hmac('sha256',$razorpayOrderId . '|' . $razorpayPaymentId,env('RAZORPAY_SECRET'));
            if (!hash_equals($generatedSignature, $razorpaySignature)) {
                return $this->error('Payment verification failed!', Response::HTTP_BAD_REQUEST);
            }
         
                // Payment is verified, create the donation and update campaign
               
                $payment = $api->payment->fetch($razorpayPaymentId);
                // If the payment is not captured, capture it
                if ($payment->status == 'authorized') {
                    $payment->capture(['amount' => $payment->amount]);  // Capture the full amount
                }
                DB::transaction(function () use ($transaction, $payment, $user,$razorpayPaymentId,$razorpaySignature,$razorpayOrderId,$donationOrderId) {
                $notes = json_decode($transaction->notes, true);

                $donation = Donation::create([
                    'campaign_id' => $transaction->campaign_id,
                    'donor_id' => $user->id,
                    'donor_name' => $notes['donor_name'],
                    'donor_email' => $notes['donor_email'],
                    'amount' => $transaction->amount, // Convert paise to rupees
                ]);
        
                // Update the payment transaction
                $transaction->update([
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'razorpay_signature' => $razorpaySignature,
                    'status' => 'completed',
                ]);

                $this->storePaymentDetails($payment,$razorpayOrderId,$donationOrderId);
                // Update the campaign's raised amount
                $campaign = Campaign::find($transaction->campaign_id);
                $campaign->raised_amount += $donation->amount;
                $campaign->save();
            });
            $orderDetails = DonationTransaction::where('razorpay_order_id', $razorpayOrderId)->where('donation_order_id', $donationOrderId)->get();
                // Clear the session data after donation is stored
                return $this->success([$orderDetails],'Donation successfully processed and verified.',Response::HTTP_OK);
        } catch (\Exception $e) {
                // Update transaction status to 'failed'
                DonationTransaction::where('razorpay_order_id', $razorpayOrderId)->update([
                    'status' => 'failed',
                ]);
            return $this->error('Payment verification failed!', Response::HTTP_BAD_REQUEST);
        }
    }

    public function storePaymentDetails($payment,$razorpayOrderId,$donationOrderId)
    {
        DonationTransaction::where('razorpay_order_id', $razorpayOrderId)->where('donation_order_id', $donationOrderId)
        ->update([
            'entity' => $payment->entity,
            'currency' => $payment->currency,
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
            'fee' => $payment->fee / 100, // Convert to INR
            'tax' => $payment->tax / 100, // Convert to INR
            'error_code' => $payment->error_code,
            'error_description' => $payment->error_description,
            'pay_created_at' => \Carbon\Carbon::createFromTimestamp($payment->created_at),
        ]);
        
    }

    /**
     * @OA\Get(
     *     path="/api/my-donations",
     *     summary="Get a list of donations made by the authenticated user",
     *     tags={"Donations"},
     *     security={{"bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of user donations",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="campaign_id", type="integer", example=2),
     *                 @OA\Property(property="campaign_name", type="string", example="Save the Forests"),
     *                 @OA\Property(property="donor_name", type="string", example="John Doe"),
     *                 @OA\Property(property="amount", type="number", format="float", example=500.00),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-18T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access"
     *     )
     * )
     */
    public function getUserDonations()
    {
        $user = Auth::user();

        $donations = Donation::where('donor_id', $user->id)
            ->with('campaign:id,name') // Assuming `name` is the campaign's title field
            ->get()
            ->map(function ($donation) {
                return [
                    'id' => $donation->id,
                    'campaign_id' => $donation->campaign_id,
                    'campaign_name' => $donation->campaign->name ?? 'Campaign not found',
                    'donor_name' => $donation->donor_name,
                    'amount' => $donation->amount,
                    'created_at' => $donation->created_at->toIso8601String(),
                ];
            });

        return $this->success($donations, 'User donations fetched successfully');
    }

}
