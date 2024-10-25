<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $event = Event::with(['participants.presences'])->findOrFail($id);
        return response()->json($event->participants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $eventId)
    {
        $request->validate([
            'cpf' => 'required|string|max:14',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $existingParticipant = Participant::where('event_id', $eventId)
            ->where('cpf', $request->cpf)
            ->first();
        
        if ($existingParticipant) {
            return response()->json(['error' => 'Já existe um participante com este CPF no evento.'], 400);
        }

        $participant = Participant::create([
            'event_id' => $eventId,
            'name' => $request->name,
            'cpf' => $request->cpf,
            'email' => $request->email,
        ]);

        return response()->json($participant, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $eventId, Participant $participant)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:participants,cpf,' . $participant->id, 
            'email' => 'required|email|max:255',
        ]);

        $participant->update($validatedData);

        return response()->json($participant, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($eventId, $participantId)
    {
        try {
            $participant = Participant::where('event_id', $eventId)->findOrFail($participantId);

            $participant->delete();

            return response()->json([
                'message' => 'Participante excluído com sucesso.'
            ], 200);

        } catch (\Exception $e) {
          
            return response()->json([
                'message' => 'Erro ao excluir participante.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
