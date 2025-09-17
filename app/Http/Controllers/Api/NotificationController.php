<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     path="/api/notifications",
     *     summary="Get notifications for a user",
     *     tags={"Notifications"},
     
     *     @OA\Response(
     *         response=200,
     *         description="Notifications retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="message", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found or no notifications"),
     *      security={{"bearer": {}}}
     * )
     */
    public function getNotifications()
    {
       $user_id = Auth::id();
       //print_r($user_id);exit;
        // Retrieve notifications for the user based on the user_id
        $notifications = Notification::where(function ($query) use ($user_id) {
            $query->where('send_to', 'all')
            ->orWhereJsonContains('user_ids', (string)$user_id);; // Check if user_id exists in user_ids array
        })->get();

        // Check if there are any notifications
        if ($notifications->isEmpty()) {
            return $this->error('No notifications found for this user', Response::HTTP_NOT_FOUND);
        }

        return $this->success($notifications, 'Notifications retrieved successfully', Response::HTTP_OK);
    }
}
