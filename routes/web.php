<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudioTranscriptionController;
use App\Http\Controllers\TranslationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/audio_listener_1', function () {
    return view('audio_listener_test');
});

Route::get('/audio_listener_2', function () {
    return view('audio_listener_test');
});

Route::post('/transcribe', [AudioTranscriptionController::class, 'transcribe']);
Route::post('/translate', [TranslationController::class, 'translate']);


