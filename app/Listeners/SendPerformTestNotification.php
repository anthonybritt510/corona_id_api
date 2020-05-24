<?php

namespace App\Listeners;

use App\Events\PerformTest;
use App\Model\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPerformTestNotification
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
     * @param  PerformTest  $event
     * @return void
     */
    public function handle(PerformTest $event)
    {
        //
        $inputPatient['user_id']           = $event->test->user_id;
        $inputPatient['notification_type'] = 'Got test';
        $inputPatient['notification_date'] = $event->test->test_date;
        $inputPatient['title']             = 'You got corona test!';
        $inputPatient['content']           = 'You got a corona test by ' . $event->test->tester_name . ' at ' . $event->test->test_date . '.';

        $inputProfessional['user_id']           = $event->test->tester_id;
        $inputProfessional['notification_type'] = 'Performed test';
        $inputProfessional['notification_date'] = $event->test->test_date;
        $inputProfessional['title']             = 'You performed corona test!';
        $inputProfessional['content']           = 'You performed a corona test to ' . $event->test->user_name . ' at ' . $event->test->test_date . '.';

        $notificationPrtient      = Notification::create($inputPatient);
        $notificationProfessional = Notification::create($inputProfessional);
    }
}
