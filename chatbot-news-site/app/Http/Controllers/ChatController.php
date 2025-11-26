<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function sendQuestion(Request $request)
    {
        $request -> validate([
            'question' => 'required|string:max:255',
        ]);

    $pythonServiceUrl = env('PYTHON_CHATBOT_URL', 'http://localhost:127.0.0.1:8001/ask');
    $apiKey = env('PYTHON_API_KEY', 'laravel_secret_key_12345');
    try{
        $response = Http::timeout(30)-> withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($pythonServiceUrl, [
            'question' => $request->question,
        ]);
        if($response->successful()){
            $data = $response->json();
            return response()->json([
                'status' => 'success',
                'answer' => $data['answer'],
            ]);
        }else{
            $errorMessage = $response->json()['detail'] ?? 'AI Service Unavailable';
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage,
            ], $response-> status());
        }
    }
    catch(\Exception $e){
        return response()->json(['status' => 'error', 'message' => 'Connection to Chatbot Service Failed: ' . $e->getMessage()], 500);
        }
    }
}
