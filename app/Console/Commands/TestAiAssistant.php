<?php

namespace App\Console\Commands;

use App\Services\AiAssistantService;
use Illuminate\Console\Command;

class TestAiAssistant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:test {module=financeiro} {--message=Como posso ajudar com este módulo?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a integração com a Gemini API';

    /**
     * Execute the console command.
     */
    public function handle(AiAssistantService $service): int
    {
        $module  = $this->argument('module');
        $message = $this->option('message');

        $this->info("🤖 Testando Assistente IA — módulo: {$module}");

        if (!config('gemini.api_key')) {
            $this->error('GEMINI_API_KEY não configurada no .env');
            return self::FAILURE;
        }

        $this->line('⏳ Consultando Gemini API...');

        $response = $service->getResponse(
            module:      $module,
            pageName:    $service->getPageName($module),
            userMessage: $message,
            history:     []
        );

        $this->info("✅ Resposta recebida:\n");
        $this->line($response);

        return self::SUCCESS;
    }
}
