<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function __construct(private AiService $aiService) {}

    /** POST /api/ai/chat — called by the frontend chatbot widget */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $session = $this->aiService->getOrCreateSession(auth()->id());
        $reply   = $this->aiService->chat($session, $request->message);

        return response()->json(['reply' => $reply]);
    }

    /** GET /api/ai/history — load previous messages for the widget */
    public function history()
    {
        $session = $this->aiService->getOrCreateSession(auth()->id());
        $messages = $session->messages()
            ->latest('created_at')
            ->limit(20)
            ->get()
            ->reverse()
            ->values()
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content]);

        return response()->json(['messages' => $messages]);
    }
}
