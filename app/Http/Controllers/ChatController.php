<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    public function send(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $sessionId = $this->sessionId($request);

        $result = $this->chatbot->reply(
            $sessionId,
            trim($data['message']),
            $request->user()?->id
        );

        return response()->json([
            'reply' => $result['reply'],
            'configured' => $result['configured'],
            'at' => now()->toIso8601String(),
        ]);
    }

    public function history(Request $request)
    {
        $sessionId = $this->sessionId($request);

        return response()->json([
            'messages' => $this->chatbot->messages($sessionId, 40),
            'configured' => $this->chatbot->isConfigured(),
        ]);
    }

    private function sessionId(Request $request): string
    {
        if (! $request->session()->has('chat_session')) {
            $request->session()->put('chat_session', (string) Str::uuid());
        }

        return (string) $request->session()->get('chat_session');
    }
}
