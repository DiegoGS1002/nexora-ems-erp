<?php

namespace App\Livewire;

use App\Services\AiAssistantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AiChatBubble extends Component
{
    public string $currentPath = '';
    public string $module      = 'suporte';
    public string $pageName    = '';
    public array  $messages    = [];
    public string $userInput   = '';
    public bool   $isLoading   = false;

    protected $listeners = ['pathChanged' => 'updatePath'];

    public function mount(): void
    {
        $this->currentPath = request()->path();
        $this->resolveModuleAndPage();

        if (Auth::check()) {
            $service = app(AiAssistantService::class);
            $this->messages = $service->getHistory(Auth::id(), $this->module);
        }
    }

    protected function resolveModuleAndPage(): void
    {
        $service = app(AiAssistantService::class);
        $this->module   = $service->detectModuleFromPath($this->currentPath);
        $this->pageName = $service->getPageName($this->currentPath);
    }

    public function updatePath(string $path): void
    {
        if ($this->currentPath !== $path) {
            $oldModule = $this->module;
            $this->currentPath = $path;
            $this->resolveModuleAndPage();

            if ($oldModule !== $this->module && Auth::check()) {
                $service = app(AiAssistantService::class);
                $this->messages = $service->getHistory(Auth::id(), $this->module);
            }
        }
    }

    public function sendMessage(AiAssistantService $service): void
    {
        $this->validate(['userInput' => 'required|string|max:1000']);

        $key = 'ai-chat:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 15)) {
            $this->addSystemMessage('⚠️ Muitas perguntas em seguida. Aguarde um momento.');
            return;
        }
        RateLimiter::hit($key, 60);

        $userMessage     = $this->userInput;
        $this->userInput = '';

        $this->messages[] = [
            'role'      => 'user',
            'content'   => $userMessage,
            'timestamp' => now()->format('H:i'),
        ];

        $this->isLoading = true;
        $this->dispatch('scrollToBottom');

        try {
            $response = $service->getResponse(
                module:      $this->module,
                pageName:    $this->pageName,
                userMessage: $userMessage,
                history:     $this->messages
            );

            $this->messages[] = [
                'role'      => 'model',
                'content'   => $response,
                'timestamp' => now()->format('H:i'),
            ];

            $service->saveHistory(Auth::id(), $this->module, $this->messages);

        } catch (\Exception $e) {
            $this->addSystemMessage('❌ Erro ao processar sua mensagem. Tente novamente.');
        } finally {
            $this->isLoading = false;
            $this->dispatch('scrollToBottom');
        }
    }

    public function useSuggestion(string $question): void
    {
        $this->userInput = $question;
        $this->dispatch('focusInput');
    }

    public function clearChat(AiAssistantService $service): void
    {
        $this->messages = [];
        if (Auth::check()) {
            $service->clearHistory(Auth::id(), $this->module);
        }
    }

    protected function addSystemMessage(string $msg): void
    {
        $this->messages[] = [
            'role'      => 'error',
            'content'   => $msg,
            'timestamp' => now()->format('H:i'),
        ];
    }

    #[Computed]
    public function suggestions(): array
    {
        return app(AiAssistantService::class)->getSuggestedQuestions($this->module, $this->pageName);
    }

    #[Computed]
    public function moduleName(): string
    {
        return app(AiAssistantService::class)->getModuleName($this->module);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.ai-chat-bubble');
    }
}
