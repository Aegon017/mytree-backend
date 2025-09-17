<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;

class FirebasePhoneAuthController extends Controller
{
    protected $auth;

    // public function __construct()
    // {
    //     // dd(env('FIREBASE_PROJECT_ID'));
    //     $this->auth = (new Factory)->createAuth();
    // }
    public function __construct()
    {
        // Initialize Firebase with the service account credentials
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))   // Firebase credentials path
            ->withProjectId(config('firebase.project_id'));        // Optionally use Firebase Project ID

        // Create Firebase Auth instance
        $this->auth = $factory->createAuth();
    }

    public function sendVerificationCode(Request $request)
    {
        // dd($this->auth);
        // Get the phone number from the request
        $phoneNumber = $request->input('phone_number');

        // Send SMS verification code
        try {
            $verification = $this->auth->sendPhoneVerificationCode($phoneNumber);
            return response()->json([
                'message' => 'Verification code sent!',
                'verification_id' => $verification->getId()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send verification code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyPhoneNumber(Request $request)
    {
        // Get the verification code and ID from the request
        $verificationId = $request->input('verification_id');
        $verificationCode = $request->input('verification_code');

        // Verify the code
        try {
            $verifiedUser = $this->auth->verifyPhoneVerificationCode($verificationId, $verificationCode);
            return response()->json([
                'message' => 'Phone number verified successfully!',
                'user' => $verifiedUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to verify phone number: ' . $e->getMessage()
            ], 500);
        }
    }
}
