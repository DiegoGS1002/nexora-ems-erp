<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RagKnowledgeSeeder;

/**
 * Comando para indexar a base de conhecimento RAG do Nexora ERP.
 *
 * Uso:
 *   php artisan ai:index-knowledge          # indexa todos os módulos
 *   php artisan ai:index-knowledge --fresh  # limpa e reindexa tudo
 */
class IndexKnowledgeCommand extends Command
{
    protected $signature = 'ai:index-knowledge
                            {--fresh : Limpa a base RAG antes de reindexar}';

    protected $description = 'Indexa a base de conhecimento RAG completa do Nexora ERP (todos os módulos, regras de negócio e fluxos)';

    public function handle(): int
    {
        $this->info('🧠 Nexora IA — Indexação da Base de Conhecimento RAG');
        $this->info('============================================================');

        if (! config('openai.api_key')) {
            $this->warn('⚠️  OPENAI_API_KEY não configurada.');
            $this->warn('   Os documentos serão indexados sem embedding vetorial.');
            $this->warn('   A busca lexical (por palavras-chave) ainda funcionará.');
            $this->newLine();
        }

        $seeder = new RagKnowledgeSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->newLine();
        $this->info('💡 Dica: Para habilitar busca semântica (vetorial), configure OPENAI_API_KEY no .env');
        $this->info('   e execute novamente: php artisan ai:index-knowledge --fresh');

        return self::SUCCESS;
    }
}

