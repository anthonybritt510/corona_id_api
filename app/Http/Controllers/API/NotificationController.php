<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 202;

    public function getNotifications(Request $request) {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)->orderBy('notification_date', 'desc')->get();
        return response()->json(array('result' => 'success', 'count' => $notifications->count(), 'data' => $notifications), $this->successStatus);
    }
}
