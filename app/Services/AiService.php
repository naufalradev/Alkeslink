<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiService
{
    private string $baseUrl;
    private string $flowId;
    private string $apiKey;
    private string $astraToken;
    private array  $astraNodeIds;

    public function __construct()
    {
        $this->baseUrl      = rtrim(config('ai.langflow_url'), '/');
        $this->flowId       = config('ai.langflow_flow');
        $this->apiKey       = config('ai.api_key', '');
        $this->astraToken   = config('ai.astra_token', '');
        $this->astraNodeIds = array_filter(
            array_map('trim', explode(',', config('ai.astra_node_ids', '')))
        );
    }

    /**
     * Get or create a chat session tied to the current browser session.
     * The session_token is reused as Langflow's session_id for conversation memory.
     */
    public function getOrCreateSession(?int $userId): ChatSession
    {
        $token = session('ai_session_token');

        if ($token) {
            $session = ChatSession::where('session_token', $token)->first();
            if ($session) return $session;
        }

        $token = (string) Str::uuid();   // UUID matches crypto.randomUUID() format
        session(['ai_session_token' => $token]);

        return ChatSession::create([
            'session_token' => $token,
            'user_id'       => $userId,
        ]);
    }

    /**
     * Send a user message to Langflow, persist both turns, and return the reply.
     */
    public function chat(ChatSession $session, string $userMessage): string
    {
        // Persist user turn
        ChatMessage::create([
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => $userMessage,
            'created_at' => now(),
        ]);

        $reply = $this->callLangflow($userMessage, $session->session_token);

        // Persist assistant turn
        ChatMessage::create([
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => $reply,
            'created_at' => now(),
        ]);

        return $reply;
    }

    /**
     * POST to Langflow /api/v1/run/{flow_id} and extract the reply text.
     */
    private function callLangflow(string $message, string $sessionId): string
    {
        $endpoint = "{$this->baseUrl}/api/v1/run/{$this->flowId}";

        // Build tweaks: inject AstraDB token into every configured node
        $tweaks = [];
        foreach ($this->astraNodeIds as $nodeId) {
            $tweaks[$nodeId] = ['token' => $this->astraToken];
        }

        $payload = [
            'output_type' => 'chat',
            'input_type'  => 'chat',
            'input_value' => $message,
            'session_id'  => $sessionId,
            'tweaks'      => $tweaks,
        ];

        try {
            $request = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key'    => $this->apiKey,
            ])->timeout(60);

            $response = $request->post($endpoint, $payload);

            if ($response->successful()) {
                return $this->extractReply($response->json());
            }

            logger()->error('Langflow HTTP error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Throwable $e) {
            logger()->error('Langflow exception: ' . $e->getMessage());
        }

        return 'Maaf, terjadi kesalahan saat menghubungi asisten. Silakan coba lagi.';
    }

    /**
     * Parse the Langflow response JSON and return the assistant text.
     *
     * Langflow v1 response structure:
     * {
     *   "outputs": [
     *     {
     *       "outputs": [
     *         {
     *           "results": {
     *             "message": { "text": "..." }
     *           }
     *         }
     *       ]
     *     }
     *   ]
     * }
     */
    private function extractReply(array $data): string
    {
        // Primary path: outputs[0].outputs[0].results.message.text
        $text = $data['outputs'][0]['outputs'][0]['results']['message']['text'] ?? null;

        if ($text !== null && trim($text) !== '') {
            return trim($text);
        }

        // Fallback: outputs[0].outputs[0].messages[0].message
        $text = $data['outputs'][0]['outputs'][0]['messages'][0]['message'] ?? null;

        if ($text !== null && trim($text) !== '') {
            return trim($text);
        }

        // Last fallback: any 'text' key anywhere in the first output
        $firstOutput = $data['outputs'][0]['outputs'][0] ?? [];
        array_walk_recursive($firstOutput, function ($val, $key) use (&$text) {
            if ($key === 'text' && $text === null && is_string($val) && trim($val) !== '') {
                $text = trim($val);
            }
        });

        return $text ?? 'Maaf, tidak ada respons dari asisten.';
    }
}
