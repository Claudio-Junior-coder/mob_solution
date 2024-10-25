<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PresenceController;


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);


    Route::get('/events/{event}/participants', [ParticipantController::class, 'index']);
    Route::post('/events/{event}/participants', [ParticipantController::class, 'store']);
    Route::put('/events/{event}/participants/{participant}', [ParticipantController::class, 'update']);
    Route::delete('/events/{event}/participants/{participant}', [ParticipantController::class, 'destroy']);


    Route::post('/events/{eventId}/attendance', [PresenceController::class, 'store']);
});