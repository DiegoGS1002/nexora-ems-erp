<?php

namespace App\Console\Commands;

use App\Ai\RagService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Popula a base vetorial RAG com documentos da base de conhecimento do Nexora ERP.
 *
 * Uso:
 *   php artisan rag:seed               # indexa tudo
 *   php artisan rag:seed --fonte=faq   # apenas FAQs
 *   php artisan rag:seed --fresh       # limpa antes de indexar
 */
class RagSeedCommand extends Command
{
    protected $signature = 'rag:seed
                            {--fonte= : Filtra por fonte específica (manual|faq|modulo|produto)}
                            {--fresh  : Remove documentos existentes antes de indexar}';

    protected $description = 'Popula a base vetorial RAG com a base de conhecimento do Nexora ERP';

    public function __construct(private RagService $rag)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! config('openai.api_key')) {
            $this->error('OPENAI_API_KEY não configurada. Configure no .env para usar o RAG.');

            return self::FAILURE;
        }

        $fonte = $this->option('fonte');

        if ($this->option('fresh')) {
            $this->warn('Removendo documentos existentes...');
            $deleted = $fonte
                ? $this->rag->deleteByFonte($fonte)
                : \App\Models\RagDocument::query()->delete();
            $this->line("  → {$deleted} documento(s) removido(s).");
        }

        $this->info('Iniciando indexação da base de conhecimento...');
        $this->newLine();

        $total = 0;

        // 1. Indexa prompts/documentação do diretório /prompts
        if (! $fonte || $fonte === 'modulo') {
            $total += $this->indexPrompts();
        }

        // 2. FAQs embutidas sobre os módulos do sistema
        if (! $fonte || $fonte === 'faq') {
            $total += $this->indexFaqs();
        }

        $this->newLine();
        $this->info("✅ Indexação concluída. {$total} documento(s) armazenado(s).");

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Fontes de conhecimento
    // -------------------------------------------------------------------------

    /**
     * Indexa arquivos Markdown do diretório /prompts como documentos de módulo.
     */
    private function indexPrompts(): int
    {
        $promptsPath = base_path('prompts');

        if (! File::isDirectory($promptsPath)) {
            $this->warn("Diretório /prompts não encontrado — pulando.");

            return 0;
        }

        $files = File::files($promptsPath);
        $count = 0;

        $this->line('<fg=cyan>Indexando prompts/documentação...</>');

        foreach ($files as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $titulo   = $this->titleFromFilename($file->getFilenameWithoutExtension());
            $conteudo = File::get($file->getPathname());

            if (mb_strlen($conteudo) < 50) {
                continue; // Ignora arquivos muito pequenos
            }

            $this->line("  → {$titulo}");

            $docs = $this->rag->storeSplit(
                titulo:    $titulo,
                conteudo:  $conteudo,
                fonte:     'modulo',
                categoria: $this->categoriaFromFilename($file->getFilenameWithoutExtension()),
                metadados: ['arquivo' => $file->getFilename()],
            );

            $count += count($docs);
        }

        $this->line("  <fg=green>{$count} chunk(s) indexado(s) de prompts.</>");

        return $count;
    }

    /**
     * Indexa FAQs embutidas sobre os módulos do sistema.
     */
    private function indexFaqs(): int
    {
        $this->line('<fg=cyan>Indexando FAQs...</>');

        $faqs = $this->getFaqs();
        $count = 0;

        foreach ($faqs as $faq) {
            $this->line("  → {$faq['titulo']}");

            $doc = $this->rag->store(
                titulo:    $faq['titulo'],
                conteudo:  $faq['conteudo'],
                fonte:     'faq',
                categoria: $faq['categoria'] ?? null,
                metadados: $faq['meta'] ?? [],
            );

            if ($doc) {
                $count++;
            }
        }

        $this->line("  <fg=green>{$count} FAQ(s) indexado(s).</>");

        return $count;
    }

    // -------------------------------------------------------------------------
    // FAQs embutidas
    // -------------------------------------------------------------------------

    private function getFaqs(): array
    {
        return [
            // NF-e
            [
                'titulo'    => 'Como emitir uma NF-e no Nexora ERP',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
Para emitir uma NF-e no Nexora ERP:
1. Acesse Fiscal > NF-e e clique em "Nova Nota Fiscal".
2. Selecione o cliente — certifique-se de que CPF/CNPJ e endereço estão preenchidos.
3. Adicione os produtos com NCM, CFOP, CST, alíquotas de ICMS, IPI, PIS e COFINS.
4. Selecione o Tipo de Operação fiscal correspondente.
5. Revise os dados e clique em "Autorizar".
6. O status mudará para "authorized" após aprovação da SEFAZ.
Caso a nota seja rejeitada, verifique o campo "Mensagem SEFAZ" para identificar o código de rejeição.
TEXT,
            ],
            [
                'titulo'    => 'Rejeição SEFAZ 702 — NCM inválido',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
A Rejeição 702 indica que o código NCM de um ou mais produtos está inválido ou desatualizado.
Solução: Acesse Cadastro > Produtos, localize o produto citado na mensagem SEFAZ e corrija o NCM.
O NCM deve ter 8 dígitos e constar na tabela NCM vigente da Receita Federal.
TEXT,
            ],
            [
                'titulo'    => 'Como cancelar uma NF-e autorizada',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
Para cancelar uma NF-e autorizada no Nexora ERP:
1. Acesse Fiscal > NF-e e localize a nota com status "authorized".
2. Clique em "Cancelar" e informe a justificativa (mínimo 15 caracteres).
3. O cancelamento deve ser feito em até 24 horas após a autorização (prazo SEFAZ).
Notas com status "rejected", "draft" ou "cancelled" não podem ser canceladas novamente.
TEXT,
            ],
            // Financeiro
            [
                'titulo'    => 'Como registrar um pagamento de conta a pagar',
                'categoria' => 'financeiro',
                'conteudo'  => <<<TEXT
Para registrar pagamento de uma conta a pagar:
1. Acesse Financeiro > Contas a Pagar.
2. Localize a conta pelo fornecedor ou vencimento.
3. Clique em "Registrar Pagamento", informe a data e conta bancária de débito.
4. O status mudará para "paid" e o fluxo de caixa será atualizado automaticamente.
TEXT,
            ],
            // Estoque
            [
                'titulo'    => 'Como fazer um lançamento de entrada no estoque',
                'categoria' => 'estoque',
                'conteudo'  => <<<TEXT
Para lançar uma entrada de estoque manualmente:
1. Acesse Estoque > Movimentação > Nova Movimentação.
2. Selecione o tipo "Entrada", o produto e a quantidade.
3. Informe o custo unitário e o motivo (ex: "Compra", "Ajuste de inventário").
4. Salve — o saldo do produto será atualizado imediatamente.
Para entradas vinculadas a NF-e de compra, use Fiscal > NF-e Entrada e o estoque é atualizado automaticamente.
TEXT,
            ],
            // RH
            [
                'titulo'    => 'Como fechar a folha de pagamento',
                'categoria' => 'rh',
                'conteudo'  => <<<TEXT
Para fechar a folha de pagamento no Nexora ERP:
1. Acesse RH > Folha de Pagamento e clique em "Nova Folha".
2. Selecione o período (mês/ano) e os funcionários ativos.
3. O sistema calculará automaticamente salário, INSS, IRRF, FGTS e benefícios.
4. Revise os holerites e clique em "Fechar Folha".
5. Após o fechamento, os holerites ficam disponíveis em RH > Holerite para download em PDF.
TEXT,
            ],
            // Usuários
            [
                'titulo'    => 'Como criar um novo usuário e definir permissões',
                'categoria' => 'administracao',
                'conteudo'  => <<<TEXT
Para criar um usuário no Nexora ERP:
1. Acesse Administração > Usuários > Novo Usuário.
2. Preencha nome, e-mail e senha temporária.
3. Atribua um ou mais Perfis de Acesso (roles) que definem as permissões por módulo.
4. Em "Módulos Contratados", marque apenas os módulos que o usuário deve acessar.
5. Salve — o usuário receberá e-mail de boas-vindas.
Para editar permissões, acesse o usuário em Administração > Usuários > Editar.
TEXT,
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function titleFromFilename(string $name): string
    {
        $name = str_replace(['-', '_'], ' ', $name);
        $name = preg_replace('/^prompt /i', '', $name);

        return ucwords($name);
    }

    private function categoriaFromFilename(string $name): string
    {
        $map = [
            'nf'         => 'fiscal',
            'fiscal'     => 'fiscal',
            'folha'      => 'rh',
            'rh'         => 'rh',
            'ponto'      => 'rh',
            'holerite'   => 'rh',
            'jornada'    => 'rh',
            'employee'   => 'rh',
            'financeiro' => 'financeiro',
            'conta'      => 'financeiro',
            'fluxo'      => 'financeiro',
            'plano'      => 'financeiro',
            'produto'    => 'estoque',
            'estoque'    => 'estoque',
            'pedido'     => 'vendas',
            'venda'      => 'vendas',
            'compra'     => 'compras',
            'cotacao'    => 'compras',
            'rota'       => 'transporte',
            'vehicle'    => 'transporte',
            'suporte'    => 'suporte',
            'usuario'    => 'administracao',
            'role'       => 'administracao',
        ];

        $lower = strtolower($name);

        foreach ($map as $keyword => $categoria) {
            if (str_contains($lower, $keyword)) {
                return $categoria;
            }
        }

        return 'geral';
    }
}

