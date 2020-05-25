<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Notification;
use Illuminate\Support\Facades\Auth;

class InstallController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 202;

    public function createSymlink(Request $request) {
    }
}
