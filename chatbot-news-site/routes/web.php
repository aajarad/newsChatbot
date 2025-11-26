<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/chatbot', function () {
    return view('chatbot');
})->name('chatbot.index');

Route::get('/ask-chatbot', function () {
    return redirect()->route('chatbot.index');
});

Route::post("/ask-chatbot", [ChatController::class,'sendQuestion']) -> name('chatbot.ask');



