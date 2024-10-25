<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Presence;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PresenceController extends Controller
{
    //
    public function store(Request $request, $eventId)
    {
        $validatedData = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'date' => 'required|date',
        ]);

        $event = Event::findOrFail($eventId);

        if ($validatedData['date'] < $event->start_date || $validatedData['date'] > $event->end_date) {
            throw ValidationException::withMessages([
                'date' => 'A data deve estar dentro do período do evento.',
            ]);
        }

        $existingPresence = Presence::where('participant_id', $validatedData['participant_id'])
            ->where('date', $validatedData['date'])
            ->first();

        if ($existingPresence) {
            throw ValidationException::withMessages([
                'date' => 'Já existe uma presença registrada para este participante nesta data.',
            ]);
        }

        $presence = new Presence();
        $presence->participant_id = $validatedData['participant_id'];
        $presence->date = $validatedData['date'];
        $presence->save();

        $participant = Participant::with('presences')->findOrFail($validatedData['participant_id']);

        return response()->json([
            'message' => 'Presença adicionada com sucesso!',
            'participant' => $participant
        ], 201);
    }

}
