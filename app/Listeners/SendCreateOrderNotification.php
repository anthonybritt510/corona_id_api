<?php

namespace App\Listeners;

use App\Events\CreateOrder;
use App\Model\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCreateOrderNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CreateOrder  $event
     * @return void
     */
    public function handle(CreateOrder $event)
    {
        //

        $input['user_id']           = $event->order->user_id;
        $input['notification_type'] = 'Create order';
        $input['notification_date'] = $event->order->order_date;
        $input['title']             = 'Your order information';
        $input['content']           = 'You ordered ' . $event->order->quantity . ' of test kit at ' . $event->order->order_date . '.';

        $notification = Notification::create($input);
    }
}
