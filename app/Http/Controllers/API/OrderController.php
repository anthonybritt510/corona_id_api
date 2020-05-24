<?php

namespace App\Http\Controllers\API;

use App\Events\CreateOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;

class OrderController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 202;

    public function createOrder(Request $request) {
        $validator = Validator::make($request->all(), [
            'card_number'      => 'required',
            'card_holder'      => 'required',
            'card_expire_date' => 'required',
            'card_cvc'         => 'required',
            'quantity'         => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(array('result' => 'error', 'message' => 'Validation error'), $this->errorStatus);
        }

        $input = $request->all();

        list($expYear, $expMonth) = explode('/', $input['card_expire_date']);
        if (strlen($expYear) == 2) {
            $expYear = '20' . $expYear;
        }

        Stripe::setApiKey(config('app.stripe_key'));
        $token = Token::create([
            'card' => [
                'number' => $input['card_number'],
                'exp_month' => intval($expMonth),
                'exp_year' =>intval($expYear),
                'cvc' => $input['card_cvc'],
            ]
        ]);

        Stripe::setApiKey(config('app.stripe_secret'));
        $charge = Charge::create([
            'amount' => $input['quantity'] * 3 * 100,
            'currency' => 'usd',
            'source' => $token->id,
            'description' => 'My First Test Charge (created for API docs)',
            'metadata' => [
                'product_name' => 'good',
                'quantity' => $input['quantity']
            ],
        ]);

        if ($charge->status == 'succeeded') {
            $user = Auth::user();
            $input['user_id'] = $user->id;
            $input['product_name'] = "Test kit";
            $order = Order::create($input);
            $order->refresh();
            event(new CreateOrder($order));
            return response()->json(array('result' => 'success', 'data' => $order), $this->successStatus);
        } else {
            return response()->json(array('result' => 'error', 'message' => 'Payment is not completed, please try again later'), $this->errorStatus);
        }
    }

    public function getOrders(Request $request) {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('order_date', 'desc')->get();
        return response()->json(array('result' => 'success', 'count' => $orders->count(), 'data' => $orders), $this->successStatus);
    }
}
