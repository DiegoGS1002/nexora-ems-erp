<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adiciona VECTOR INDEX nativo ao campo embedding da tabela rag_documents.
     *
     * MySQL 9.7 suporta VECTOR INDEX para busca ANN (Approximate Nearest Neighbor)
     * via vec_distance_cosine de forma acelerada em nível de engine (InnoDB).
     *
     * MySQL 8.x �� coluna é LONGTEXT, índice vetorial não aplicável (skip).
     */
    public function up(): void
    {
        $version = DB::selectOne('SELECT VERSION() AS v')->v;
        $major   = (int) explode('.', $version)[0];

        if ($major >= 9) {
            // Verifica se o índice já existe para ser idempotente
            $indexExists = DB::selectOne("
                SELECT COUNT(*) AS cnt
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME   = 'rag_documents'
                  AND INDEX_NAME   = 'idx_rag_embedding'
            ");

            if ($indexExists && $indexExists->cnt === 0) {
                try {
                    DB::statement('ALTER TABLE rag_documents ADD VECTOR INDEX idx_rag_embedding (embedding)');
                } catch (\Exception $e) {
                    // VECTOR INDEX may not be supported on this MySQL build – skip silently
                }
            }
        }
    }

    public function down(): void
    {
        $version = DB::selectOne('SELECT VERSION() AS v')->v;
        $major   = (int) explode('.', $version)[0];

        if ($major >= 9) {
            $indexExists = DB::selectOne("
                SELECT COUNT(*) AS cnt
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME   = 'rag_documents'
                  AND INDEX_NAME   = 'idx_rag_embedding'
            ");

            if ($indexExists && $indexExists->cnt > 0) {
                DB::statement('ALTER TABLE rag_documents DROP INDEX idx_rag_embedding');
            }
        }
    }
};

