<?php

namespace App\Ai;

use App\Models\TicketSuporte;
use App\Models\User;
use App\Ai\RagService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AgenteService
{
    /** Iterações do loop de tool calling — aumentado para suportar pipelines multi-tool */
    private const MAX_ITERATIONS = 8;

    public function __construct(
        private ToolRegistry $registry,
        private ContextBuilder $contextBuilder,
        private FallbackResponder $fallback,
        private RagService $rag
    ) {}

    /**
     * Generate AI response with function calling support.
     * Architecture: User → IA → (decide) → Tool → Backend → Result → IA → Response
     * Fallback chain: OpenAI → Gemini → FallbackResponder
     */
    public function gerarResposta(User $user, TicketSuporte $ticket, Collection $mensagens): ?string
    {
        $ultimaMensagem = $mensagens->where('is_suporte', false)->where('is_ia', false)->last();
        $textoUsuario   = $ultimaMensagem?->conteudo ?? '';

        // Recupera contexto RAG relevante para a mensagem do usuário
        $ragContext = $textoUsuario ? $this->rag->buildContext($textoUsuario) : '';

        $messages     = $this->contextBuilder->buildMessages($user, $ticket, $mensagens, $ragContext ?: null);
        $tools        = $this->registry->definitions();

        // 1. Try OpenAI
        if (config('openai.api_key')) {
            try {
                $result = $this->runOpenAiLoop($messages, $tools, $user->id);
                if ($result) {
                    Log::info('AgenteService: response generated via OpenAI', [
                        'ticket_id' => $ticket->id,
                        'user_id'   => $user->id,
                        'provider'  => 'openai',
                    ]);

                    return $result;
                }
            } catch (\Throwable $e) {
                Log::warning('AgenteService: OpenAI failed, trying Gemini', [
                    'ticket_id' => $ticket->id,
                    'error'     => substr($e->getMessage(), 0, 300),
                ]);
            }
        }

        // 2. Try Gemini
        if (config('gemini.api_key')) {
            try {
                $result = $this->runGeminiRequest($messages, $textoUsuario);
                if ($result) {
                    Log::info('AgenteService: response generated via Gemini', [
                        'ticket_id' => $ticket->id,
                        'user_id'   => $user->id,
                        'provider'  => 'gemini',
                    ]);

                    return $result;
                }
            } catch (\Throwable $e) {
                Log::warning('AgenteService: Gemini failed, using static fallback', [
                    'ticket_id' => $ticket->id,
                    'error'     => substr($e->getMessage(), 0, 300),
                ]);
            }
        }

        // 3. Static fallback
        Log::warning('AgenteService: all AI providers failed, using FallbackResponder', [
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id,
            'provider'  => 'fallback',
        ]);

        return $this->fallback->gerarResposta($ticket, $textoUsuario);
    }

    /**
     * Run OpenAI conversation loop with tool calling.
     */
    private function runOpenAiLoop(array $messages, array $tools, int $userId): ?string
    {
        $iteration = 0;

        while ($iteration < self::MAX_ITERATIONS) {
            $iteration++;

            $response = OpenAI::chat()->create([
                'model'       => config('openai.model', 'gpt-4o-mini'),
                'messages'    => $messages,
                'tools'       => $tools,
                'tool_choice' => 'auto',
                'max_tokens'  => 2000,
                'temperature' => 0.4,
            ]);

            $choice = $response->choices[0] ?? null;
            if (! $choice) {
                return null;
            }

            $finishReason = $choice->finishReason ?? '';
            $message      = $choice->message;

            $messages[] = [
                'role'       => 'assistant',
                'content'    => $message->content ?? null,
                'tool_calls' => $message->toolCalls ?? null,
            ];

            if ($finishReason === 'tool_calls' && ! empty($message->toolCalls)) {
                foreach ($message->toolCalls as $toolCall) {
                    $functionName = $toolCall->function->name;
                    $arguments    = json_decode($toolCall->function->arguments, true) ?? [];
                    $toolResult   = $this->registry->execute($functionName, $arguments, $userId);

                    $messages[] = [
                        'role'         => 'tool',
                        'tool_call_id' => $toolCall->id,
                        'content'      => json_encode($toolResult, JSON_UNESCAPED_UNICODE),
                    ];
                }
                continue;
            }

            if ($finishReason === 'stop' && $message->content) {
                return $message->content;
            }

            break;
        }

        return null;
    }

    /**
     * Run Gemini request (without tool calling — context only).
     */
    private function runGeminiRequest(array $messages, string $userMessage): ?string
    {
        $apiKey = config('gemini.api_key');
        $model  = config('gemini.model', 'gemini-2.0-flash');

        // Build a single prompt from the conversation
        $systemContent = '';
        $contents      = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemContent = $msg['content'];
                continue;
            }
            $role = $msg['role'] === 'user' ? 'user' : 'model';
            if (! empty($msg['content'])) {
                $contents[] = [
                    'role'  => $role,
                    'parts' => [['text' => $msg['content']]],
                ];
            }
        }

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemContent]],
            ],
            'contents'           => $contents,
            'generationConfig'   => [
                'temperature'     => 0.4,
                'maxOutputTokens' => (int) config('gemini.max_tokens', 2048),
            ],
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout((int) config('gemini.request_timeout', 60))
            ->post($url, $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Gemini API error: ' . $response->status() . ' ' . $response->body());
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        return $text ?: null;
    }
}
