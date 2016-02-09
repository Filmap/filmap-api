<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class FilmWasStored extends Event
{
    use SerializesModels;

    // List of users to send the notification
    public $usersList;

    // The user who marked the film
    public $user;

    // The film that has been watched
    public $film;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($usersList, $user, $film)
    {
        $this->usersList = $usersList;
        $this->film = $film;
        $this->user = $user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
