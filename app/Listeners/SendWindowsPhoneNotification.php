<?php

namespace App\Listeners;

use App\Events\FilmWasStored;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWindowsPhoneNotification
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
     * @param  FilmWasStored  $event
     * @return void
     */
    public function handle(FilmWasStored $event)
    {
        dd($event);
    }
}
