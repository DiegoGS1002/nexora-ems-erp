<?php

namespace App\Ai;

use App\Models\TicketSuporte;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AgenteService
{
    private const MAX_ITERATIONS = 3;

    public function __construct(
        private ToolRegistry $registry,
        private ContextBuilder $contextBuilder,
        private FallbackResponder $fallback
    ) {}

    /**
     * Generate AI response with function calling support.
     * Architecture: User → IA → (decide) → Tool → Backend → Result → IA → Response
     */
    public function gerarResposta(User $user, TicketSuporte $ticket, Collection $mensagens): ?string
    {
        if (! config('openai.api_key')) {
            Log::warning('AgenteService: OpenAI API key not configured');
            return null;
        }

        try {
            $messages = $this->contextBuilder->buildMessages($user, $ticket, $mensagens);
            $tools = $this->registry->definitions();

            return $this->runConversationLoop($messages, $tools, $user->id);
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();

            // Special handling for rate limits - return useful fallback response
            if (str_contains($errorMsg, 'rate limit')) {
                Log::warning('AgenteService: OpenAI rate limit exceeded - using fallback', [
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                ]);

                // Get last user message
                $ultimaMensagem = $mensagens->where('is_suporte', false)
                    ->where('is_ia', false)
                    ->last();

                return $this->fallback->gerarResposta(
                    $ticket,
                    $ultimaMensagem?->conteudo ?? ''
                );
            }

            Log::error('AgenteService: error generating response', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'error' => $errorMsg,
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Run conversation loop with function calling.
     */
    private function runConversationLoop(array $messages, array $tools, int $userId): ?string
    {
        $iteration = 0;

        while ($iteration < self::MAX_ITERATIONS) {
            $iteration++;

            $response = OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-4o-mini'),
                'messages' => $messages,
                'tools' => $tools,
                'tool_choice' => 'auto',
                'max_tokens' => 1500,
                'temperature' => 0.7,
            ]);

            $choice = $response->choices[0] ?? null;
            if (! $choice) {
                return null;
            }

            $finishReason = $choice->finishReason ?? '';
            $message = $choice->message;

            // Add assistant message to conversation
            $messages[] = [
                'role' => 'assistant',
                'content' => $message->content ?? null,
                'tool_calls' => $message->toolCalls ?? null,
            ];

            // If AI wants to call tools
            if ($finishReason === 'tool_calls' && ! empty($message->toolCalls)) {
                foreach ($message->toolCalls as $toolCall) {
                    $functionName = $toolCall->function->name;
                    $arguments = json_decode($toolCall->function->arguments, true) ?? [];

                    // Execute tool
                    $toolResult = $this->registry->execute($functionName, $arguments, $userId);

                    // Add tool result to messages
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall->id,
                        'content' => json_encode($toolResult, JSON_UNESCAPED_UNICODE),
                    ];
                }

                // Continue loop to get final answer with tool results
                continue;
            }

            // Got final answer
            if ($finishReason === 'stop' && $message->content) {
                return $message->content;
            }

            // Safety: if we got here with unexpected finish reason, break
            break;
        }

        return null;
    }
}


