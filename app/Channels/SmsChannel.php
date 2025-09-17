<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // if (method_exists($notification, 'toSms')) {
        //     $notification->toSms($notifiable);
        // }
        $smsData = $notification->toSms($notifiable);
        $url = 'https://smslogin.co/v3/api.php';
        $params = [
            'username' => 'MYTREE',
            'apikey' => '17cb6bf26e826eb64035',
            'mobile' => $smsData['mobile'],
            'senderid' => 'MYTREN',
            'message' => $smsData['message'],
            'templateid' => $smsData['templateid'],
        ];
        $response = Http::get($url, $params);

        if ($response->successful()) {
            \Log::info("SMS sent successfully: " . $response->body());
        } else {
            \Log::error("Failed to send SMS: " . $response->body());
        }
    }
}
