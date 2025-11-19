<?php
// app/Http/Controllers/ParticipantsViewController.php

namespace App\Http\Controllers;

use App\Models\Participant;

class ParticipantsViewController extends Controller
{
    public function index()
    {
        $participants = Participant::orderBy('created_at', 'desc')->paginate(50);

        $stats = [
            'total' => Participant::count(),
            'this_week' => Participant::where('week_identifier', Participant::getCurrentWeekIdentifier())->count(),
        ];

        return view('contest/participants-view', compact('participants', 'stats'));
    }
}
