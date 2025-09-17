<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginOtpRequest;
use App\Http\Requests\Api\UserLoginRequest;
use Illuminate\Http\Request;

use App\Models\User;
use App\Notifications\SendOtpNotification;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * @category	Controller
 * @package		Auth Controller
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */
// * @  OA\Server(
//     *         url="https://arboraid.co/api/",
//     *         description="Base URL for API"
//     *     )

 /**
 * @OA\Info(title="Trees API", version="1.0")
 * @OA\Tag(name="Auth", description="Operations related to Auth")
 * @OA\SecurityScheme(
 *     securityScheme="bearer",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter 'Bearer {token}' to access the endpoints."
 * )

 * @OA\Header(
 *         header="Accept",
 *         description="Expected response format",
 *         required=true,
 *         @OA\Schema(type="string", example="application/json")
 *     ),
 *     @OA\Header(
 *         header="Content-Type",
 *         description="Format of the request body",
 *         required=true,
 *         @OA\Schema(type="string", example="application/json")
 *     )
 */

class AuthController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     summary="Register a new user with referral and send OTP",
     *     description="This endpoint creates a new user with a temporary password, logs referrals, and sends an OTP. user_type : 1-user,2-organization",
     *     operationId="signUp",
     *      tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile_prefix", "mobile", "user_type","fcm_token"},
     *             @OA\Property(property="mobile_prefix", type="string", example="+91", description="Mobile country code prefix"),
     *             @OA\Property(property="mobile", type="string", example="9876543210", description="User's mobile number"),
     *             @OA\Property(property="fcm_token", type="string", example="GDFDFD86676HKKJGG", description="fcm_token"),
     *             @OA\Property(property="user_type", type="integer", example="1", description="1- user,2- company"),
     *             @OA\Property(property="referral_code", type="string", example="ABCD1234", description="Referral code of the referring user (optional)"),
     *         ),
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="User successfully registered and OTP sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP sent successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user_id", type="integer", example=1, description="ID of the newly created user"),
     *             ),
     *         ),
     *     ),
     * 
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid referral code or other input errors")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unable to process the request at this time")
     *         )
     *     ),
     * 
     *     @OA\SecurityScheme(
     *         securityScheme="bearerAuth",
     *         type="http",
     *         scheme="bearer",
     *         bearerFormat="JWT"
     *     )
     * )
     */
     
    public function signUp(UserLoginRequest $request)
    {
        // Create the user
        $user = User::create([
            'mobile_prefix' => $request->mobile_prefix,
            'mobile' => $request->mobile,
            'fcm_token' => $request->fcm_token,
            'user_type' => $request->user_type,
            'referred_by' => $request->referral_code ? User::where('referral_code',$request->referral_code)->value('id') : null,
            'password' => bcrypt(Str::random(8)), // Temporary password
        ]);

        // Log the referral if necessary
        if ($user->referred_by) {
            // Store the referral in a separate table, if needed
            DB::table('referrals')->insert([
                'user_id' => $user->id,
                'referred_by' => $user->referred_by,
            ]);
        }


        return $this->sendOtp($user);
    }

    /**
     * @OA\Post(
     *     path="/api/signin",
     *     summary="User Sign In",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile"},
     *             @OA\Property(property="mobile", type="string", example="1234567890"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP sent"),
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */

    public function signIn(UserLoginOtpRequest $request)
    {
        $user = User::withTrashed()->where('mobile', $request->mobile)->first();
        if ($request->mobile === '9876543210') {
            return $this->testLogin();
        }
        if ($user?->deleted_at !== null) {
            return $this->error(
                "Your account is currently deactivated. To access your account, please reactivate it.",
                Response::HTTP_LOCKED,
                ['deactivated' => true]
            );
        }
        if (!$user) {
            return $this->error(trans('user.not_found'),Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->sendOtp($user);
    }
    
    private function testLogin()
    {
        $user = User::firstOrCreate(
            ['mobile' => '9876543210'],
            [
                'mobile_prefix' => '+91',
                'user_type' => 1,
                'password' => bcrypt(Str::random(8)),
            ]
        );

        $user->otp = '123456';
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        return $this->success([], trans('user.otp_sent'), Response::HTTP_OK);
    }

    
    private function sendOtp($user)
    {
        // $otp = 123456;
        $otp = rand(100000, 999999); // Generate a 6-digit OTP
        //rand(100000, 999999);
        //Str::random(6); // Generate a 6-digit OTP
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();
        $templateid = '1707175231272775110';
        $message = "Your OTP to sign in to My Tree is {$otp} . Please do not share it with anyone.";
        //$message = "Thank you for registering with SOWJANYA STUDIOS. Your one-time password for registering with us is {$otp}.";

        $user->notify(new SendOtpNotification($otp, $message, $templateid));
        // $user->notify(new SendOtpNotification($otp,$message,$templateid));
        // Notification::send($user, new SendOtpNotification($otp));
        return $this->success([],trans('user.otp_sent'),Response::HTTP_OK);
    }


    /**
     * @OA\Post(
     *     path="/api/resend-otp",
     *     summary="Resend OTP",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile"},
     *             @OA\Property(property="mobile", type="string", example="1234567890"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP sent"),
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */

    public function resendOtp(UserLoginOtpRequest $request)
    {
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return $this->error(trans('user.not_found'),Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Generate a new OTP
        return $this->sendOtp($user);
    }


/**
     * @OA\Post(
     *     path="/api/verify-otp",
     *     summary="Verify OTP",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile", "otp"},
     *             @OA\Property(property="mobile", type="string", example="1234567890"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your_token_here"),
     *             @OA\Property(property="message", type="string", example="Success"),
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid OTP")
     * )
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'otp' => 'required|string',
        ]);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user || $user->otp !== $request->otp) {
        // if (!$user || $user->otp !== $request->otp || now()->isPast($user->otp_expires_at)) {
            return $this->error(trans('user.otp_invalid'),Response::HTTP_BAD_REQUEST);
        }

        // Clear OTP
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Create a token
        $token = $user->createToken('authToken')->plainTextToken;
        return $this->success($token,trans('user.success'),Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user and invalidate token",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token missing or invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *      security={{"bearer": {}}}
     * )
     */
    public function logout(Request $request)
    {
        // Revoke the user's current token
        $request->user()->currentAccessToken()->delete();

        return $this->success([],trans('user.logout'),Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/update-fcm-token",
     *     summary="Update FCM token for authenticated user",
     *     tags={"Auth"},
     *     security={{"bearer": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"fcm_token"},
     *             @OA\Property(property="fcm_token", type="string", example="NEW_FCM_TOKEN_123"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FCM token updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="FCM token updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return $this->success([], 'FCM token updated successfully', Response::HTTP_OK);
    }
    
    /**
     * @OA\Get(
     *     path="/api/app-config",
     *     summary="Get app configuration",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="App config fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="is_guest_login", type="boolean", example=true)
     *             )
     *         )
     *     )
     * )
     */
    public function getAppConfig()
    {
        return $this->success([
            'is_guest_login' => true,
        ], 'Success');
    }
    
     /**
 * @OA\Post(
 *     path="/api/deactivate-account",
 *     summary="Deactivate authenticated user's account",
 *     tags={"Auth"},
 *     security={{"bearer": {}}},
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="reason", type="string", example="No longer interested"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Account deactivated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Account deactivated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - Invalid token",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthorized")
 *         )
 *     )
 * )
 */
public function deactivateAccount(Request $request)
{
    $user = $request->user();

    // Optional: Log reason for deactivation
    if ($request->filled('reason')) {
        DB::table('account_deactivations')->insert([
            'user_id' => $user->id,
            'reason' => $request->reason,
            'created_at' => now(),
        ]);
    }

    // Soft delete user
    $user->delete();

    // Revoke all tokens
    $user->tokens()->delete();

    return $this->success([], 'Account deactivated successfully', Response::HTTP_OK);
}
    /**
     * @OA\Post(
     *     path="/api/reactivate-account",
     *     summary="Reactivate user account",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile"},
     *             @OA\Property(property="mobile", type="string", example="1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Account reactivated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Your account has been reactivated successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=400, description="Account is already active")
     * )
     */
    public function reactivateAccount(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string'
        ]);

        // find user including deactivated ones
        $user = User::withTrashed()->where('mobile', $request->mobile)->first();

        if (!$user) {
            return $this->error('User not found', Response::HTTP_NOT_FOUND);
        }

        if ($user->deleted_at === null) {
            return $this->error('Account is already active', Response::HTTP_BAD_REQUEST);
        }

        // restore soft deleted account
        $user->restore();

        return $this->success(null, 'Your account has been reactivated successfully.', Response::HTTP_OK);
    }

}
