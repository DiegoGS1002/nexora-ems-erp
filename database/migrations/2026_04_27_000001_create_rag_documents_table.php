<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de documentos RAG.
     *
     * MySQL 9.x → coluna VECTOR(1536) nativa + VECTOR INDEX (busca ANN via vec_distance_cosine)
     * MySQL 8.x → coluna LONGTEXT para armazenar o embedding como JSON; similaridade calculada em PHP
     */
    public function up(): void
    {
        Schema::create('rag_documents', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->mediumText('conteudo');
            $table->string('fonte')->default('manual'); // manual | faq | modulo | ticket | produto
            $table->string('categoria')->nullable();
            $table->json('metadados')->nullable();
            $table->unsignedInteger('tokens')->default(0);
            $table->timestamps();
        });

        $version = DB::selectOne('SELECT VERSION() AS v')->v;
        $major   = (int) explode('.', $version)[0];

        if ($major >= 9) {
            // MySQL 9.x: tipo VECTOR nativo (armazenamento eficiente, 4 bytes/float)
            // Busca por similaridade é feita em PHP via VECTOR_TO_STRING() na leitura
            DB::statement('ALTER TABLE rag_documents ADD COLUMN embedding VECTOR(1536) NOT NULL');
        } else {
            // MySQL 8.x: embedding como JSON em LONGTEXT
            DB::statement('ALTER TABLE rag_documents ADD COLUMN embedding LONGTEXT NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rag_documents');
    }
};

