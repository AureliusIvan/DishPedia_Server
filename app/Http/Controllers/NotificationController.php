<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications;

        return response()->json([
            'notifications' => $notifications,
        ], 200);
    }

    /**
     * Mark a notification as read.
     *
     * @param  Request  $request
     * @param  Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read.',
        ], 200);
    }
}