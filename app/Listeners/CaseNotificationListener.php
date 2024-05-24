<?php

namespace App\Listeners;

use App\Events\CaseNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CaseNotificationListener
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
     * @param  \App\Events\CaseNotificationEvent  $event
     * @return void
     */
    public function handle(CaseNotificationEvent $event)
    {
        //
    }
}
