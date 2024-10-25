<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(Event::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:datainicio',
        ]);

        $event = Event::create($request->all());

        return response()->json($event, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $event = Event::with('participants')->findOrFail($id);
        return response()->json($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $event->update($request->all());

        return response()->json($event, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id); 
        $event->delete(); 
        
        return response()->json(['message' => 'Evento deletado com sucesso.'], 200);
    }

}
