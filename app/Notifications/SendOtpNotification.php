<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SendOtpNotification extends Notification
{
    use Queueable;

    protected $otp;
    protected $message;
    protected $templateid;

    /**
     * Create a new notification instance.
     *
     * @param int $otp
     */
    public function __construct($otp,$message,$templateid)
    {
        $this->otp = $otp;
        $this->message = $message;
        $this->templateid = $templateid;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [\App\Channels\SmsChannel::class];
        // return ['sms']; // Define a custom channel for SMS
    }


    /**
     * Prepare data for SMS channel.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toSms($notifiable)
    {
        // $message = str_replace('{#var#}', $this->otp, $this->message);

        return [
            'mobile' => $notifiable->mobile,
            'message' => $this->message,
            'templateid' => $this->templateid,
        ];
    }

    /**
     * Send the SMS notification.
     *
     * @param object $notifiable
     */
    // public function toSms($notifiable)
    // {
    //     $username = 'Ambati';
    //     $apikey = 'f325218b7fdc12c125b4';
    //     $senderid = 'SOWSTD';
    //     $templateid = '1207169355519345595';
    //     $message = "Thank you for registering with SOWJANYA STUDIOS. Your one-time password for registering with us is {$this->otp}.";
    //     \Log::info("SMS sent templateid: " . $notifiable->templateid);
    //     \Log::info("SMS sent message: " . $notifiable->message);
    //     // Prepare the API endpoint and parameters
    //     $url = 'https://smslogin.co/v3/api.php';
    //     $params = [
    //         'username' => $username,
    //         'apikey' => $apikey,
    //         'mobile' => $notifiable->mobile, // Assumes the user has a `mobile` attribute
    //         'senderid' => $senderid,
    //         'message' => $message,
    //         'templateid' => $notifiable->templateid,
    //     ];

    //     // Send the SMS via the API
    //     $response = Http::get($url, $params);

    //     // Log the response for debugging
    //     if ($response->successful()) {
    //         \Log::info("SMS sent successfully: " . $response->body());
    //     } else {
    //         \Log::error("Failed to send SMS: " . $response->body());
    //     }
    // }
}
