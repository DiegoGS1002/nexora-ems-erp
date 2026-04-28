<?php

namespace Database\Seeders;

use App\Models\SefazDiagnostic;
use Illuminate\Database\Seeder;

class SefazDiagnosticsSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            // ─── Autorização ──────────────────────────────────────────────────
            ['code' => '101', 'titulo' => 'NF-e não encontrada na SEFAZ', 'causa' => 'A NF-e informada não existe na base da SEFAZ.', 'solucao' => "Verifique se a chave de acesso está correta. Acesse **Fiscal > NF-e** e confira os dados da nota.", 'module' => 'fiscal', 'severity' => 'warning', 'tags' => ['chave_acesso']],
            ['code' => '102', 'titulo' => 'NF-e cancelada', 'causa' => 'A NF-e já foi cancelada anteriormente.', 'solucao' => "Esta nota não pode ser reativada. Emita uma nova NF-e em **Fiscal > NF-e > Nova Nota**.", 'module' => 'fiscal', 'severity' => 'info', 'tags' => ['cancelamento']],
            ['code' => '110', 'titulo' => 'Uso denegado', 'causa' => 'A NF-e foi denegada pela SEFAZ do destinatário.', 'solucao' => "A nota não tem validade jurídica. Verifique a situação cadastral do destinatário na Receita Federal.", 'module' => 'fiscal', 'severity' => 'error', 'tags' => ['denegacao', 'destinatario']],
            ['code' => '202', 'titulo' => 'Falha de comunicação SEFAZ', 'causa' => 'Instabilidade na comunicação com o WebService da SEFAZ.', 'solucao' => "1. Aguarde alguns minutos e tente reenviar\n2. Verifique a conectividade em **Fiscal > NF-e**\n3. Verifique o certificado digital em **Fiscal > Configurações**", 'module' => 'fiscal', 'severity' => 'warning', 'tags' => ['comunicacao', 'certificado']],
            ['code' => '203', 'titulo' => 'Emissor não habilitado para produção', 'causa' => 'O CNPJ do emitente não está habilitado para emissão no ambiente de produção.', 'solucao' => "1. Acesse **Fiscal > Configurações**\n2. Verifique se o certificado digital está válido e é do tipo A1 ou A3\n3. Confirme se a empresa está habilitada na SEFAZ do seu estado\n4. Se estiver em homologação, mude para produção apenas quando estiver pronto", 'module' => 'configuracao', 'severity' => 'error', 'tags' => ['certificado', 'producao', 'homologacao'], 'related' => ['208']],
            ['code' => '204', 'titulo' => 'Número/Série duplicado', 'causa' => 'Já existe uma NF-e autorizada com o mesmo número e série.', 'solucao' => "1. Acesse **Fiscal > Configurações**\n2. Atualize o número da próxima NF-e para o próximo número disponível\n3. Tente emitir novamente", 'module' => 'fiscal', 'severity' => 'error', 'tags' => ['numeracao', 'serie'], 'related' => ['784']],
            ['code' => '205', 'titulo' => 'NF-e já autorizada', 'causa' => 'Esta NF-e já foi autorizada anteriormente.', 'solucao' => "A nota já está válida. Acesse **Fiscal > NF-e** para visualizá-la e baixar o XML/DANFE.", 'module' => 'fiscal', 'severity' => 'info', 'tags' => ['autorizacao']],
            ['code' => '207', 'titulo' => 'CNPJ emitente inválido', 'causa' => 'O CNPJ do emitente está inválido ou com dígitos verificadores incorretos.', 'solucao' => "1. Acesse **Administração > Empresas**\n2. Corrija o CNPJ da empresa emitente\n3. Salve e tente emitir novamente", 'module' => 'configuracao', 'severity' => 'error', 'tags' => ['cnpj', 'emitente']],
            ['code' => '208', 'titulo' => 'CNPJ do certificado difere do emitente', 'causa' => 'O CNPJ do certificado digital não corresponde ao CNPJ da empresa emitente no sistema.', 'solucao' => "1. Acesse **Fiscal > Configurações**\n2. Verifique se o certificado pertence ao CNPJ correto\n3. Substitua o certificado se necessário\n4. O certificado deve pertencer exatamente ao CNPJ emitente", 'module' => 'configuracao', 'severity' => 'error', 'tags' => ['certificado', 'cnpj', 'emitente']],
            ['code' => '214', 'titulo' => 'CNPJ emitente não cadastrado na SEFAZ', 'causa' => 'O CNPJ do emitente não está na base de dados da SEFAZ ou não está em situação regular.', 'solucao' => "1. Verifique a situação cadastral em https://www.receita.fazenda.gov.br\n2. Acesse **Administração > Empresas** e confirme o CNPJ\n3. Contate a SEFAZ do seu estado para habilitação", 'module' => 'configuracao', 'severity' => 'error', 'tags' => ['cnpj', 'emitente', 'receita_federal']],
            ['code' => '215', 'titulo' => 'CPF/CNPJ destinatário inválido', 'causa' => 'O CPF ou CNPJ do destinatário está com formato ou dígitos verificadores inválidos.', 'solucao' => "1. Acesse **Cadastro > Clientes**\n2. Localize o cliente da nota\n3. Corrija o CPF/CNPJ\n4. Salve e tente emitir novamente", 'module' => 'cadastro', 'severity' => 'error', 'tags' => ['cnpj', 'cpf', 'destinatario', 'cliente'], 'related' => ['539']],
            ['code' => '216', 'titulo' => 'IE do emitente inválida', 'causa' => 'A Inscrição Estadual do emitente está inválida para o estado informado.', 'solucao' => "1. Acesse **Administração > Empresas**\n2. Verifique a Inscrição Estadual (IE)\n3. Corrija conforme o padrão de validação do seu estado e salve", 'module' => 'configuracao', 'severity' => 'error', 'tags' => ['ie', 'inscricao_estadual', 'emitente']],
            ['code' => '238', 'titulo' => 'Número máximo da série atingido', 'causa' => 'O número de NF-e na série atual atingiu o limite máximo (999.999.999).', 'solucao' => "1. Acesse **Fiscal > Configurações**\n2. Altere para a próxima série disponível (ex: série 2)\n3. Recomece a numeração a partir de 1", 'module' => 'fiscal', 'severity' => 'error', 'tags' => ['numeracao', 'serie']],
            ['code' => '539', 'titulo' => 'CNPJ destinatário inválido na Receita Federal', 'causa' => 'O CNPJ do destinatário não existe ou não está em situação regular na Receita Federal.', 'solucao' => "1. Acesse **Cadastro > Clientes**\n2. Verifique o CNPJ do cliente\n3. Consulte https://www.receita.fazenda.gov.br/Aplicacoes/SSL/ATCTA/CNPJ/ConsultaPublica.asp\n4. Corrija e tente emitir novamente", 'module' => 'cadastro', 'severity' => 'error', 'tags' => ['cnpj', 'destinatario', 'cliente', 'receita_federal']],
            // ─── Produto / Fiscal ─────────────────────────────────────────────
            ['code' => '702', 'titulo' => 'Código NCM inválido', 'causa' => 'O código NCM (Nomenclatura Comum do Mercosul) de um ou mais produtos está inválido ou desatualizado na tabela TIPI.', 'solucao' => "1. Acesse **Cadastro > Produtos**\n2. Localize o(s) produto(s) da nota\n3. Corrija o NCM com o código correto de **8 dígitos**\n4. Consulte a tabela NCM vigente: https://www.mdic.gov.br\n5. Salve e emita novamente\n\n**Dica:** NCMs inválidos ocorrem quando um código foi extinto ou reclassificado na última atualização da TIPI.", 'module' => 'cadastro', 'severity' => 'error', 'tags' => ['ncm', 'produto', 'tipi'], 'related' => ['778']],
            ['code' => '741', 'titulo' => 'CFOP inválido para a operação', 'causa' => 'O CFOP (Código Fiscal de Operações e Prestações) informado é inválido ou incompatível com o tipo de operação.', 'solucao' => "1. Acesse **Fiscal > Tipos de Operação**\n2. Revise o CFOP configurado\n3. CFOPs de saída iniciam em **5** (intraestadual) ou **6** (interestadual)\n4. CFOPs de entrada iniciam em **1** (intraestadual) ou **2** (interestadual)\n5. Consulte a tabela CFOP vigente para confirmar o código correto", 'module' => 'fiscal', 'severity' => 'error', 'tags' => ['cfop', 'operacao_fiscal']],
            ['code' => '778', 'titulo' => 'NCM inexistente na TIPI', 'causa' => 'O código NCM informado não existe na tabela TIPI/NCM vigente. O item com problema é identificado entre colchetes — ex: [item8] = 8º produto da nota.', 'solucao' => "1. Identifique qual produto é o item indicado entre colchetes (ex: [item8] = 8º produto)\n2. Acesse **Cadastro > Produtos**\n3. Abra o produto e localize o campo **NCM**\n4. Consulte a tabela TIPI: https://www.gov.br/receitafederal/pt-br\n5. Atualize o NCM com o código de **8 dígitos** correto\n6. Salve e emita a nota novamente em **Fiscal > NF-e**", 'module' => 'cadastro', 'severity' => 'error', 'tags' => ['ncm', 'produto', 'tipi'], 'related' => ['702']],
            ['code' => '784', 'titulo' => 'Duplicidade — NF-e em contingência já autorizada', 'causa' => 'NF-e já autorizada em contingência. Esta nota foi emitida em modo contingência e já recebeu autorização.', 'solucao' => "A nota já foi autorizada. Acesse **Fiscal > NF-e** para localizar a nota autorizada e baixar o XML.", 'module' => 'fiscal', 'severity' => 'info', 'tags' => ['contingencia', 'duplicidade'], 'related' => ['204']],
            ['code' => '805', 'titulo' => 'Falha no schema XML', 'causa' => 'O XML da NF-e não está no formato correto exigido pela SEFAZ. Tags obrigatórias ausentes ou com formato incorreto.', 'solucao' => "Este é um erro interno do sistema. Entre em contato com o suporte técnico via WhatsApp: **(32) 98450-2345**\n\nInforme o número e série da nota e a mensagem completa retornada.", 'module' => 'fiscal', 'severity' => 'error', 'tags' => ['xml', 'schema', 'sistema']],
            ['code' => '938', 'titulo' => 'CFOP incompatível com o tipo de operação', 'causa' => 'O CFOP informado é incompatível com a natureza da operação declarada. Ex: CFOP de venda intraestadual em uma operação interestadual, ou CFOP de devolução sem nota referenciada.', 'solucao' => "1. Identifique se a operação é intraestadual (5xxx) ou interestadual (6xxx)\n2. Verifique em **Fiscal > Tipos de Operação** se o CFOP configurado está correto\n3. Para notas de devolução, verifique se a nota de origem está referenciada\n4. Para operações com substituição tributária, use CFOPs específicos (ex: 5405, 6405)", 'module' => 'fiscal', 'severity' => 'error', 'tags' => ['cfop', 'operacao_fiscal', 'substituicao_tributaria'], 'related' => ['741']],
            ['code' => '999', 'titulo' => 'Erro interno SEFAZ', 'causa' => 'Erro interno não catalogado na SEFAZ. Pode ser instabilidade no sistema da Receita Estadual.', 'solucao' => "1. Aguarde 10-15 minutos e tente novamente\n2. Verifique o status da SEFAZ do seu estado\n3. Se persistir após 1 hora, contate o suporte: **(32) 98450-2345**", 'module' => 'fiscal', 'severity' => 'warning', 'tags' => ['sefaz', 'instabilidade']],
        ];

        foreach ($registros as $r) {
            SefazDiagnostic::updateOrCreate(
                ['rejection_code' => $r['code']],
                [
                    'titulo'         => $r['titulo'],
                    'causa'          => $r['causa'],
                    'solucao'        => $r['solucao'],
                    'module'         => $r['module'] ?? 'fiscal',
                    'severity'       => $r['severity'] ?? 'error',
                    'related_codes'  => $r['related'] ?? null,
                    'tags'           => $r['tags'] ?? null,
                    'active'         => true,
                ]
            );
        }

        $this->command->info('SefazDiagnosticsSeeder: ' . count($registros) . ' diagnósticos carregados.');
    }
}

