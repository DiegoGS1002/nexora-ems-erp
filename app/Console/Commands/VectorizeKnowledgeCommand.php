<?php

namespace App\Console\Commands;

use App\Ai\RagService;
use App\Models\RagDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Gera embeddings vetoriais para documentos RAG que ainda estão sem vetor.
 *
 * Uso:
 *   php artisan ai:vectorize           # gera embeddings para os sem vetor
 *   php artisan ai:vectorize --batch=5 # processa N de cada vez
 */
class VectorizeKnowledgeCommand extends Command
{
    protected $signature = 'ai:vectorize
                            {--batch=10 : Número de documentos a processar por vez}
                            {--all : Regenera embeddings de TODOS os documentos}';

    protected $description = 'Gera embeddings vetoriais (OpenAI) para documentos RAG sem vetor';

    public function handle(RagService $rag): int
    {
        if (! config('openai.api_key')) {
            $this->error('❌ OPENAI_API_KEY não configurada no .env');
            return self::FAILURE;
        }

        $batch = max(1, (int) $this->option('batch'));
        $all   = (bool) $this->option('all');

        if ($all) {
            $this->warn('⚠️  Regenerando embeddings de TODOS os documentos...');
            // Reset all to zero vector for MySQL 9.x (hasNativeVector is private, check MySQL version)
            $version = DB::selectOne('SELECT VERSION() AS v')->v;
            if ((int) explode('.', $version)[0] >= 9) {
                $zeroVec = '[' . implode(',', array_fill(0, 1536, 0)) . ']';
                DB::statement("UPDATE rag_documents SET embedding = STRING_TO_VECTOR(?)", [$zeroVec]);
            } else {
                DB::statement("UPDATE rag_documents SET embedding = NULL");
            }
        }

        $total = RagDocument::count();
        if ($total === 0) {
            $this->warn('Nenhum documento RAG encontrado. Execute: php artisan ai:index-knowledge');
            return self::FAILURE;
        }

        $this->info("🧠 Vectorizando base RAG ({$total} documentos total)...");
        $this->newLine();

        $bar     = $this->output->createProgressBar($total);
        $updated = 0;
        $errors  = 0;
        $offset  = 0;

        do {
            $count = $rag->generateMissingEmbeddings($batch);
            $updated += $count;
            $bar->advance($count);
            $offset += $batch;

            if ($count === 0) {
                break;
            }

            // Small delay to respect API rate limits
            if ($count > 0) {
                usleep(200_000); // 200ms
            }

        } while ($offset < $total);

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Embeddings gerados: {$updated} documentos vectorizados.");

        if ($errors > 0) {
            $this->warn("⚠️  {$errors} documentos falharam (verifique os logs).");
        }

        return self::SUCCESS;
    }
}


