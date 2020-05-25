<?php

namespace App\Http\Controllers\API;

use App\Events\PerformTest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Test;
use App\Model\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 202;

    public function addTest(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id'        => 'required',
            'tester_id'      => 'required',
            'viral_count'    => 'required',
            'is_positive'    => 'required',
            'testkit_number' => 'required',
            'is_confirmed'   => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(array('result' => 'error', 'message' => 'Validation error'), $this->errorStatus);
        }

        $input = $request->all();

        $user = User::find($input['user_id']);
        $input['user_name'] = $user->getFullname();

        $tester = User::find($input['tester_id']);
        $input['tester_name'] = $tester->getFullname();

        $test = Test::create($input);
        event(new PerformTest($test->refresh()));
        return response()->json(array('result' => 'success', 'data' => $test), $this->successStatus);
    }

    public function getTestsByPatient(Request $request) {
        $user = Auth::user();
        $tests = Test::where('user_id', $user->id)->orderBy('test_date', 'desc')->get();
        return response()->json(array('result' => 'success', 'count'=>$tests->count(), 'data' => $tests), $this->successStatus);
    }

    public function getTestsByProfessional(Request $request) {
        $user = Auth::user();
        $tests = Test::where('tester_id', $user->id)->orderBy('test_date', 'desc')->get();
        return response()->json(array('result' => 'success', 'count' => $tests->count(), 'data' => $tests), $this->successStatus);
    }
}
