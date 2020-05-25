<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstallController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 202;

    public function createSymlink(Request $request) {
        $target = storage_path();
        $link = public_path('storage');
        symlink($target, $link);
    }
}
