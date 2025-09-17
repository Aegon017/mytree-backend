<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Google_Client;
// use Google\Client as GoogleClient;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->view = 'Admin.';
    }

    // Show all notifications
    public function index()
    {
        $notifications = Notification::all();

        return view(
            $this->view . 'notifications.index',
            compact(['notifications'])
        );
    }

    // Show form for creating a new notification
    public function create()
    {
        return view(
            $this->view . 'notifications.create'
        );
    }

    // Store a new notification
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'send_to' => 'required|in:all,specific',
            'user_ids' => 'nullable|array',
        ]);

        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'send_to' => $request->send_to,
            'user_ids' => $request->send_to == 'specific' ? $request->user_ids : null,
        ]);

        // Send the notification
        $this->sendPushNotification($notification);

        return redirect()->route('notifications.index')->with('success', 'Notification sent successfully!');
    }

    // Show form for editing a notification
    public function edit(Notification $notification)
    {
        $users = User::all(); 
        
        return view(
            $this->view . 'notifications.edit',
            compact(['notification','users'])
        );
    }

    // Update the notification
    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'send_to' => 'required|in:all,specific',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'send_to' => $request->send_to,
            'user_ids' => $request->send_to == 'specific' ? $request->user_ids : null,
        ]);

        // Send the updated notification
        $this->sendPushNotification($notification);

        return redirect()->route('notifications.index')->with('success', 'Notification updated and sent successfully!');
    }

    // Delete the notification
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notification deleted successfully!');
    }

    // Send push notification to mobile devices using FCM


    public function sendPushNotification($notification)
{
    $projectId = 'mytree-b169f';
    // $serviceAccountPath = storage_path('app/firebase/firebase-service-account.json');
    $serviceAccountPath = storage_path('app/firebase/firebase-service-account.json');

    // Load the service account credentials
    // $googleClient = new \Google_Client();
    $googleClient = new Google_Client();
    //new Google_Client();
    $googleClient->setAuthConfig($serviceAccountPath);
    $googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $googleClient->refreshTokenWithAssertion();
    $accessToken = $googleClient->getAccessToken()['access_token'];

    $users = $notification->user_ids && count($notification->user_ids)
        ? User::whereIn('id', $notification->user_ids)->whereNotNull('fcm_token')->get()
        : User::whereNotNull('fcm_token')->get();

    $fcmTokens = $users->pluck('fcm_token')->toArray();
    // dd($fcmTokens);
    if (empty($fcmTokens)) {
        return response()->json(['success' => false, 'message' => 'No FCM tokens found for the users.']);
    }

    // Note: The HTTP v1 API sends one message per request, so loop tokens or use topics

    foreach ($fcmTokens as $token) {
        $message = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => "Admin Notification",
                    "body" => $notification->message,
                ],
                "android" => [
                    "priority" => "high"
                ],
                "apns" => [
                    "headers" => [
                        "apns-priority" => "10"
                    ]
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/$projectId/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        $response = curl_exec($ch);
        curl_close($ch);

        // You can log or handle $response here
    }

    return response()->json(['success' => true, 'message' => 'Notifications sent via HTTP v1 API']);
}

    public function sendPushNotificationOldWoking($notification)
    {
        $users = $notification->user_ids && count($notification->user_ids)
            ? User::whereIn('id', $notification->user_ids)->whereNotNull('fcm_token')->get()
            : User::whereNotNull('fcm_token')->get();

        $fcmTokens = $users->pluck('fcm_token')->toArray();
        if (empty($fcmTokens)) {
            return response()->json(['success' => false, 'message' => 'No FCM tokens found for the users.']);
        }

        $SERVER_API_KEY = 'f9a4d51469b0b458f86fe7068cc6250dd1b84624';
        //env('FCM_SERVER_KEY');

        $data = [
            "registration_ids" => $fcmTokens,
            "notification" => [
                "title" => "Admin Notification",
                "body" => $notification->message,
                "sound" => "default"
            ],
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
dd($response);
        return response()->json(['success' => true, 'response' => json_decode($response)]);
    }

    // private function sendPushNotificationOld($notification)
    // {
    //     // FCM logic (same as before, using Firebase Cloud Messaging API)
    //     $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    //     $serverKey = 'YOUR_SERVER_KEY';  // Replace with your Firebase Server Key

    //     $fields = [
    //         'to' => $notification->send_to == 'all' ? '/topics/all' : $notification->user_ids,
    //         'notification' => [
    //             'title' => $notification->title,
    //             'body' => $notification->message,
    //         ],
    //     ];

    //     $headers = [
    //         'Authorization: key=' . $serverKey,
    //         'Content-Type: application/json'
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    //     $result = curl_exec($ch);
    //     if ($result === false) {
    //         die('FCM Send Error: ' . curl_error($ch));
    //     }

    //     curl_close($ch);
    // }

    public function loadUsers()
    {
        // Retrieve all users
        $users = User::all(); // You can adjust this based on your needs (e.g., filtering active users)
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}
