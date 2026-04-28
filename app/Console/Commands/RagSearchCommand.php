<?php

namespace App\Console\Commands;

use App\Ai\RagService;
use Illuminate\Console\Command;

/**
 * Testa a busca vetorial RAG.
 *
 * Uso:
 *   php artisan rag:search "como emitir nfe"
 *   php artisan rag:search "cancelar nota" --limit=3 --categoria=fiscal
 */
class RagSearchCommand extends Command
{
    protected $signature = 'rag:search
                            {query : Texto de busca}
                            {--limit=5 : Número de resultados}
                            {--categoria= : Filtra por categoria}
                            {--fonte= : Filtra por fonte}';

    protected $description = 'Testa a busca vetorial RAG (similaridade coseno MySQL 9.x)';

    public function __construct(private RagService $rag)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $query     = $this->argument('query');
        $limit     = (int) $this->option('limit');
        $categoria = $this->option('categoria');
        $fonte     = $this->option('fonte');

        $this->info("Buscando: \"{$query}\"");
        $this->newLine();

        $results = $this->rag->search($query, $limit, $categoria, $fonte);

        if ($results->isEmpty()) {
            $this->warn('Nenhum documento encontrado.');

            return self::SUCCESS;
        }

        foreach ($results as $i => $doc) {
            $n = $i + 1;
            $this->line("<fg=cyan>[{$n}] {$doc->titulo}</> — score: <fg=green>{$doc->score}%</> | distância: {$doc->distancia}");
            $this->line("    fonte: {$doc->fonte}" . ($doc->categoria ? " | categoria: {$doc->categoria}" : ''));
            $this->line('    ' . mb_substr($doc->conteudo, 0, 200) . (mb_strlen($doc->conteudo) > 200 ? '...' : ''));
            $this->newLine();
        }

        return self::SUCCESS;
    }
}

