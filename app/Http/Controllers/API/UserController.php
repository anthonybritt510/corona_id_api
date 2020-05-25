<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\Test;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 202;

    public function login(Request $request)
    {
        if (Auth::attempt(['document_number' => request('document_number'), 'password' => request('document_number')])) {
            $user = Auth::user();
            $user['token'] =  $user->createToken('MyApp')->accessToken;
            $user['test_status'] = $this->checkUserStatus($user);
            return response()->json(array('result' => 'success',  'data' => $user), $this->successStatus);
        } else {
            return response()->json(array('result' => 'error', 'message' => 'Unauthorised'), $this->errorStatus);
        }
    }

    public function register(Request $request)
    {
        Log::debug('Register api callback from "' . $request->server('REMOTE_ADDR') . '"');
        Log::debug($request);

        $validator = Validator::make($request->all(), [
            'first_name'        => 'required',
            'last_name'         => 'required',
            'gender'            => 'required',
            'birthday'          => 'required',
            'document_number'   => 'required',
            'document_type'     => 'required',
            'user_photo'        => 'required',
            'document_photo'    => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('result' => 'error', 'message' => 'validation error');
            return response()->json($result, $this->errorStatus);
        }

        if (User::where('document_number', $request->get('document_number'))->count() > 0) {
            $user = User::where('document_number', $request->get('document_number'))->first();
            $input = $request->all();
            return response()->json(array('result' => 'success', 'data' => $user), $this->errorStatus);
        }

        $input = $request->all();

        $birthdayDate = strtotime(str_replace('-', '/', $input['birthday']));
        $input['birthday'] = date('Y-m-d', $birthdayDate);

        if (isset($input['expiration_date']) && $input['expiration_date'] != '') {
            $expirationDate = strtotime(str_replace('-', '/', $input['expiration_date']));
            $input['expiration_date'] = date('Y-m-d', $expirationDate);

            Log::debug($input['expiration_date']);
        }

        // Upload user photo and save the link to model
        $pathUserPhoto = $request->file('user_photo')->store('users', 'public');
        $input['user_photo'] = Storage::url($pathUserPhoto);

        // Upload document photo and save the link to model
        $pathDocumentPhoto = $request->file('document_photo')->store('documents', 'public');
        $input['document_photo'] = Storage::url($pathDocumentPhoto);

        // If the photo of professional license exist, upload and save it
        if(isset($input['professional_license_photo'])) {
            $pathProfessionalLicensePhoto = $request->file('professional_license_photo')->store('licenses', 'public');
            $input['professional_license_photo'] = Storage::url($pathProfessionalLicensePhoto);
            $input['is_professional'] = 1;
        }

        $input['password'] = bcrypt($input['document_number']);
        $user = User::create($input);
        $user->refresh();
        $user['token'] =  $user->createToken('MyApp')->accessToken;
        $user['name'] =  $user->getFullname();
        $user['test_status'] = $this->checkUserStatus($user);
        return response()->json(array('result'=>'success', 'data' => $user ), $this->successStatus);
    }

    public function userDetails(Request $request)
    {
        Log::debug('User Detail api callback from "' . $request->server('REMOTE_ADDR') . '"');
        $user = Auth::user();
        $user['test_status'] = $this->checkUserStatus($user);
        $result = array('result' => 'success', 'data' => $user);
        return response()->json($result, $this->successStatus);
    }

    public function sendVerifyEmail(Request $request) {

        Log::debug('User Detail api callback from "' . $request->server('REMOTE_ADDR') . '"');
        Log::debug($request);

        if(!isset($request->email)) {
            return response()->json(array('result' => 'error', 'message' => 'Need email address'), $this->errorStatus);
        }
        $user = Auth::user();
        $user->email = $request->email;
        $user->email_verify_code = rand(1000, 9999);
        $user->save();
        $user->refresh();
        
        Mail::raw('Email Verification code is ' . $user->email_verify_code, function($message) use($user) {
            $message->to($user->email)
                ->subject('Corona ID email verification');
        });
        // Mail::to($user)->send(new MailableClass());
        return response()->json(array('result' => 'success', 'data' => $user), $this->successStatus);
    }

    public function confirmVerifyEmail(Request $request) {
        if(!isset($request->verify_code)) {
            return response()->json(array('result' => 'error', 'message' => 'You should enter correct verification code.'), $this->errorStatus);
        }
        $user = Auth::user();
        if($request->verify_code == $user->email_verify_code) {
            return response()->json(array('result' => 'success', 'data' => $user), $this->successStatus);
        } else {
            return response()->json(array('result' => 'error', 'message' => 'You should enter correct verification code.'), $this->errorStatus);
        }
    }

    public function getUserInfo(Request $request) {
        if(!isset($request->user_id)) {
            return response()->json(array('result' => 'error', 'message' => 'Need user id'), $this->errorStatus);
        }
        $user = User::find($request->user_id);
        if($user) {
            $success['id'] = $user->id;
            $success['first_name'] = $user->first_name;
            $success['last_name'] = $user->last_name;
            $success['user_photo'] = $user->user_photo;
            return response()->json(array('result' => 'success', 'data' => $success), $this->successStatus);
        } else {
            return response()->json(array('result' => 'error', 'message' => 'There is no exist the user information you want to get'), $this->errorStatus);
        }
    }

    public function setOrderInfo(Request $request) {
        $validator = Validator::make($request->all(), [
            'order_contact_name' => 'required',
            'order_email'        => 'required',
            'order_phone_number' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(array('result' => 'error', 'message' => 'Validateion error'), $this->errorStatus);
        }
        $input = $request->all();
        $user = Auth::user();
        if($user->update($input)) {
            $user->refresh();
            return response()->json(array('result' => 'success', 'data' => $user), $this->successStatus);
        } else {
            return response()->json(array('result' => 'error', 'message' => "Save order information is failed, please try again later."), $this->errorStatus);
        }
    }

    protected function checkUserStatus(User $user) {
        $latestTest = Test::where('user_id', $user->id)->orderBy('test_date', 'desc')->first();

        if($latestTest == null) {
            return 'not_tested_for_1_week';
        }

        $latestTestDate = date_create($latestTest->test_date);

        if (date_add($latestTestDate, date_interval_create_from_date_string('1 week')) < date_create('now')) {
            return 'not_tested_for_1_week';
        } else {
            if ($latestTest->is_positive == 1) {
                return 'positive';
            } else {
                return 'negative';
            }
        }
    }
}
