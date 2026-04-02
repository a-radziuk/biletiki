<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function show(Event $event)
    {
        $event->load('sections');

        return view('events.show', compact('event'));
    }
}
