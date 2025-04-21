<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudioTranscriptionController;
use App\Http\Controllers\TranslationController;

Route::get('/', function () {
    return view('audio_listener_test');
});


Route::post('/transcribe', [AudioTranscriptionController::class, 'transcribe']);
Route::post('/translate', [TranslationController::class, 'translate']);


