<?php

namespace App\Ai;

use App\Models\RagDocument;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Serviço RAG (Retrieval-Augmented Generation).
 *
 * Estratégia automática por versão do MySQL:
 *  - MySQL 9.x → VECTOR(1536) nativo + VECTOR_TO_STRING() para cálculo de similaridade em PHP
 *  - MySQL 8.x → LONGTEXT com JSON + similaridade coseno calculada em PHP
 *
 * Recuperação híbrida:
 *  - Busca lexical (códigos exatos e termos técnicos)
 *  - Busca vetorial (contexto semântico)
 *  - Merge/rerank por score final
 */
class RagService
{
    private const EMBEDDING_MODEL     = 'text-embedding-3-small';
    private const DIMENSIONS          = 1536;
    private const MAX_TOKENS_PER_CHUNK = 500;

    private ?bool $mysqlNativeVector = null;

    // -------------------------------------------------------------------------
    // Detecção de versão
    // -------------------------------------------------------------------------

    private function hasNativeVector(): bool
    {
        if ($this->mysqlNativeVector === null) {
            $version                 = DB::selectOne('SELECT VERSION() AS v')->v;
            $this->mysqlNativeVector = (int) explode('.', $version)[0] >= 9;
        }

        return $this->mysqlNativeVector;
    }

    // -------------------------------------------------------------------------
    // Escrita
    // -------------------------------------------------------------------------

    /**
     * Armazena um documento com seu embedding vetorial.
     *
     * @param  string      $titulo    Título / identificador legível
     * @param  string      $conteudo  Texto do chunk
     * @param  string      $fonte     Origem: manual | faq | modulo | produto | ticket
     * @param  string|null $categoria Categoria opcional (ex: 'fiscal', 'rh')
     * @param  array       $metadados Dados extras para rastreabilidade
     */
    public function store(
        string $titulo,
        string $conteudo,
        string $fonte = 'manual',
        ?string $categoria = null,
        array $metadados = []
    ): ?RagDocument {
        $embedding = $this->embed($conteudo);

        if ($embedding === null) {
            Log::warning('RagService: falha ao gerar embedding — documento não armazenado.', ['titulo' => $titulo]);

            return null;
        }

        $tokens   = $this->estimateTokens($conteudo);
        $metaJson = json_encode($metadados, JSON_UNESCAPED_UNICODE);
        $now      = now()->toDateTimeString();
        $vecJson  = $this->vectorToJson($embedding);

        if ($this->hasNativeVector()) {
            DB::statement(
                'INSERT INTO rag_documents (titulo, conteudo, fonte, categoria, metadados, tokens, embedding, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, STRING_TO_VECTOR(?), ?, ?)',
                [$titulo, $conteudo, $fonte, $categoria, $metaJson, $tokens, $vecJson, $now, $now]
            );
        } else {
            DB::statement(
                'INSERT INTO rag_documents (titulo, conteudo, fonte, categoria, metadados, tokens, embedding, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$titulo, $conteudo, $fonte, $categoria, $metaJson, $tokens, $vecJson, $now, $now]
            );
        }

        return RagDocument::latest('id')->first();
    }

    /**
     * Armazena múltiplos chunks de um documento grande (splitting automático).
     *
     * @param  string      $titulo    Título base (será sufixado com " [parte N]")
     * @param  string      $conteudo  Texto completo a ser dividido
     * @param  string      $fonte     Origem
     * @param  string|null $categoria Categoria
     * @param  array       $metadados Metadados extras
     * @return RagDocument[]
     */
    public function storeSplit(
        string $titulo,
        string $conteudo,
        string $fonte = 'manual',
        ?string $categoria = null,
        array $metadados = []
    ): array {
        $chunks = $this->splitIntoChunks($conteudo);
        $docs   = [];

        foreach ($chunks as $i => $chunk) {
            $chunkTitulo = count($chunks) > 1 ? "{$titulo} [parte " . ($i + 1) . ']' : $titulo;
            $doc         = $this->store($chunkTitulo, $chunk, $fonte, $categoria, $metadados);

            if ($doc) {
                $docs[] = $doc;
            }
        }

        return $docs;
    }

    /**
     * Remove todos os documentos de uma fonte específica.
     */
    public function deleteByFonte(string $fonte): int
    {
        return RagDocument::where('fonte', $fonte)->delete();
    }

    /**
     * Remove documento por ID.
     */
    public function delete(int $id): bool
    {
        return (bool) RagDocument::destroy($id);
    }

    // -------------------------------------------------------------------------
    // Busca
    // -------------------------------------------------------------------------

    /**
     * Busca os K documentos mais relevantes para a query com abordagem híbrida
     * (palavras-chave + similaridade coseno).
     *
     * @param  string      $query     Texto da pergunta/busca
     * @param  int         $limit     Número de resultados
     * @param  string|null $categoria Filtra por categoria (opcional)
     * @param  string|null $fonte     Filtra por fonte (opcional)
     * @param  float       $threshold Distância máxima (0 = idêntico, 2 = oposto; coseno)
     */
    public function search(
        string $query,
        int $limit = 5,
        ?string $categoria = null,
        ?string $fonte = null,
        float $threshold = 1.0
    ): Collection {
        $keywordDocs = $this->searchKeyword($query, max(8, $limit * 2), $categoria, $fonte);

        $embedding = $this->embed($query);

        if ($embedding === null) {
            Log::warning('RagService: falha ao gerar embedding da query; usando busca por palavras-chave.', [
                'query' => mb_substr($query, 0, 120),
                'hits'  => $keywordDocs->count(),
            ]);

            return $keywordDocs->take($limit)->values();
        }

        $vectorDocs = $this->hasNativeVector()
            ? $this->searchNative($embedding, $limit, $categoria, $fonte, $threshold)
            : $this->searchPhp($embedding, $limit, $categoria, $fonte, $threshold);

        return $this->mergeHybridResults($keywordDocs, $vectorDocs, $limit);
    }

    /** MySQL 9.x — vec_distance_cosine() nativo */
    private function searchNative(array $embedding, int $limit, ?string $categoria, ?string $fonte, float $threshold): Collection
    {
        // MySQL 9.x armazena como VECTOR; recupera como JSON via VECTOR_TO_STRING
        // e calcula similaridade em PHP (funções de distância nativas chegam em versões futuras)
        $q = RagDocument::query()
            ->selectRaw('id, titulo, conteudo, fonte, categoria, metadados, tokens, VECTOR_TO_STRING(embedding) AS embedding');

        if ($categoria) {
            $q->where('categoria', $categoria);
        }

        if ($fonte) {
            $q->where('fonte', $fonte);
        }

        return $q->get()
            ->map(function ($doc) use ($embedding) {
                $vec = json_decode($doc->embedding ?? '[]', true);

                if (empty($vec)) {
                    return null;
                }

                return $this->formatRow($doc, $this->cosineDistance($embedding, $vec));
            })
            ->filter(fn ($r) => $r !== null && $r->distancia <= $threshold)
            ->sortBy('distancia')
            ->take($limit)
            ->values();
    }

    /** MySQL 8.x — similaridade coseno em PHP */
    private function searchPhp(array $queryEmbedding, int $limit, ?string $categoria, ?string $fonte, float $threshold): Collection
    {
        $q = RagDocument::query()->select(['id', 'titulo', 'conteudo', 'fonte', 'categoria', 'metadados', 'tokens', 'embedding']);

        if ($categoria) {
            $q->where('categoria', $categoria);
        }

        if ($fonte) {
            $q->where('fonte', $fonte);
        }

        return $q->get()
            ->map(function (RagDocument $doc) use ($queryEmbedding) {
                $vec = json_decode($doc->embedding ?? '[]', true);

                if (empty($vec)) {
                    return null;
                }

                return $this->formatRow($doc, $this->cosineDistance($queryEmbedding, $vec));
            })
            ->filter(fn ($r) => $r !== null && $r->distancia <= $threshold)
            ->sortBy('distancia')
            ->take($limit)
            ->values();
    }

    private function formatRow(object $r, float $distancia): object
    {
        return (object) [
            'id'        => $r->id,
            'titulo'    => $r->titulo,
            'conteudo'  => $r->conteudo,
            'fonte'     => $r->fonte,
            'categoria' => $r->categoria,
            'metadados' => is_array($r->metadados ?? null) ? $r->metadados : json_decode($r->metadados ?? '{}', true),
            'tokens'    => $r->tokens,
            'distancia' => round($distancia, 4),
            'score'     => round((1 - $distancia) * 100, 1),
        ];
    }

    /**
     * Busca lexical para códigos exatos, mensagens e termos técnicos.
     * Funciona mesmo sem OPENAI_API_KEY.
     */
    private function searchKeyword(string $query, int $limit, ?string $categoria, ?string $fonte): Collection
    {
        $terms = $this->extractKeywordTerms($query);
        if (empty($terms)) {
            return collect();
        }

        $q = RagDocument::query()->select(['id', 'titulo', 'conteudo', 'fonte', 'categoria', 'metadados', 'tokens']);

        if ($categoria) {
            $q->where('categoria', $categoria);
        }

        if ($fonte) {
            $q->where('fonte', $fonte);
        }

        $q->where(function ($inner) use ($terms) {
            foreach ($terms as $term) {
                $like = '%' . $term . '%';
                $inner->orWhere('titulo', 'like', $like)
                    ->orWhere('conteudo', 'like', $like)
                    ->orWhere('categoria', 'like', $like)
                    ->orWhere('fonte', 'like', $like);
            }
        });

        return $q->limit(max(20, $limit * 3))
            ->get()
            ->map(function (RagDocument $doc) use ($terms) {
                $text  = mb_strtolower($doc->titulo . ' ' . $doc->conteudo . ' ' . ($doc->categoria ?? ''));
                $score = 0;

                foreach ($terms as $term) {
                    $termLower = mb_strtolower($term);
                    if ($termLower !== '' && str_contains($text, $termLower)) {
                        $score += mb_strlen($termLower) >= 5 ? 20 : 12;
                    }
                }

                // Distância sintética: quanto maior score lexical, menor distância.
                $distance = max(0.0, 1.0 - min(100, $score) / 100);

                return $this->formatRow($doc, $distance);
            })
            ->sortBy('distancia')
            ->take($limit)
            ->values();
    }

    /**
     * Extrai termos úteis da query para busca lexical (inclui códigos numéricos de rejeição).
     *
     * @return string[]
     */
    private function extractKeywordTerms(string $query): array
    {
        $query = mb_strtolower(trim($query));
        if ($query === '') {
            return [];
        }

        preg_match_all('/\b\d{3,}\b/u', $query, $numericCodes);

        $tokens = preg_split('/[^\p{L}\p{N}_-]+/u', $query) ?: [];
        $tokens = array_values(array_filter($tokens, fn ($t) => mb_strlen($t) >= 3));

        $terms = array_merge($numericCodes[0] ?? [], $tokens);
        $terms = array_values(array_unique($terms));

        return array_slice($terms, 0, 12);
    }

    /**
     * Combina resultados vetoriais e lexicais, evitando duplicados por ID.
     */
    private function mergeHybridResults(Collection $keywordDocs, Collection $vectorDocs, int $limit): Collection
    {
        $merged = collect();

        // Vetorial primeiro (semântico), depois lexical (exato), removendo duplicatas.
        foreach ($vectorDocs as $doc) {
            $merged->put((string) $doc->id, $doc);
        }

        foreach ($keywordDocs as $doc) {
            $key = (string) $doc->id;
            if (! $merged->has($key)) {
                $merged->put($key, $doc);
                continue;
            }

            // Se existir nos dois, fica com melhor score.
            $current = $merged->get($key);
            if (($doc->score ?? 0) > ($current->score ?? 0)) {
                $merged->put($key, $doc);
            }
        }

        return $merged->values()
            ->sortBy('distancia')
            ->take($limit)
            ->values();
    }

    /**
     * Formata os documentos recuperados em um bloco de contexto para o prompt.
     *
     * @param  string $query  Pergunta original do usuário
     * @param  int    $limit  Número de chunks a recuperar
     */
    public function buildContext(string $query, int $limit = 4): string
    {
        $docs = $this->search($query, $limit);

        if ($docs->isEmpty()) {
            return '';
        }

        $context  = "## Base de Conhecimento (RAG)\n";
        $context .= "Os trechos abaixo foram recuperados da base de conhecimento do Nexora ERP:\n\n";

        foreach ($docs as $i => $doc) {
            $n        = $i + 1;
            $context .= "### [{$n}] {$doc->titulo}";

            if ($doc->categoria) {
                $context .= " *(categoria: {$doc->categoria})*";
            }

            $context .= " — relevância: {$doc->score}%\n";
            $context .= $doc->conteudo . "\n\n";
        }

        $context .= "---\n*Use os trechos acima como base para sua resposta quando relevantes.*\n";

        return $context;
    }

    // -------------------------------------------------------------------------
    // Embedding
    // -------------------------------------------------------------------------

    /**
     * Gera o embedding vetorial de um texto via OpenAI.
     *
     * @return float[]|null
     */
    public function embed(string $text): ?array
    {
        if (! config('openai.api_key')) {
            Log::warning('RagService: OPENAI_API_KEY não configurada.');

            return null;
        }

        try {
            $response = OpenAI::embeddings()->create([
                'model'      => self::EMBEDDING_MODEL,
                'input'      => $this->truncateForEmbedding($text),
                'dimensions' => self::DIMENSIONS,
            ]);

            return $response->embeddings[0]->embedding ?? null;
        } catch (\Throwable $e) {
            Log::error('RagService: erro ao gerar embedding.', ['error' => substr($e->getMessage(), 0, 300)]);

            return null;
        }
    }

    // -------------------------------------------------------------------------
    // Similaridade coseno (PHP)
    // -------------------------------------------------------------------------

    private function cosineDistance(array $a, array $b): float
    {
        $dot = $normA = $normB = 0.0;
        $len = min(count($a), count($b));

        for ($i = 0; $i < $len; $i++) {
            $dot   += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 1.0;
        }

        return 1.0 - max(-1.0, min(1.0, $dot / (sqrt($normA) * sqrt($normB))));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Converte array de floats para JSON compatível com STRING_TO_VECTOR() do MySQL.
     * Formato: '[0.123, 0.456, ...]'
     */
    private function vectorToJson(array $embedding): string
    {
        return '[' . implode(',', $embedding) . ']';
    }

    /**
     * Divide texto em chunks de ~MAX_TOKENS_PER_CHUNK tokens (estimativa por palavras).
     *
     * @return string[]
     */
    private function splitIntoChunks(string $text): array
    {
        $words   = preg_split('/\s+/', trim($text));
        $chunks  = [];
        $current = [];

        foreach ($words as $word) {
            $current[] = $word;

            if (count($current) >= self::MAX_TOKENS_PER_CHUNK) {
                $chunks[]  = implode(' ', $current);
                $current   = [];
            }
        }

        if (! empty($current)) {
            $chunks[] = implode(' ', $current);
        }

        return $chunks ?: [$text];
    }

    /**
     * Trunca o texto para no máximo 8191 tokens (limite do text-embedding-3-small).
     * Estimativa: 1 token ≈ 4 caracteres.
     */
    private function truncateForEmbedding(string $text): string
    {
        $maxChars = 8191 * 4;

        return mb_strlen($text) > $maxChars ? mb_substr($text, 0, $maxChars) : $text;
    }

    /**
     * Estimativa da quantidade de tokens (1 token ≈ 4 chars em português).
     */
    private function estimateTokens(string $text): int
    {
        return (int) ceil(mb_strlen($text) / 4);
    }
}

