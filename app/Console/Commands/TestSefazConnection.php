<?php

namespace App\Console\Commands;

use App\Services\NFeService;
use Illuminate\Console\Command;

class TestSefazConnection extends Command
{
    protected $signature = 'nfe:test-connection';
    protected $description = 'Testa conexão com SEFAZ e valida configuração';

    public function handle(NFeService $nfeService): int
    {
        $this->info('🔍 Testando conexão com SEFAZ...');
        $this->newLine();

        try {
            // 1. Validar configurações
            $this->info('1️⃣  Validando configurações...');
            $config = [
                'Ambiente' => config('nfe.environment') === 1 ? 'Produção' : 'Homologação',
                'UF' => config('nfe.uf'),
                'CNPJ' => config('nfe.cnpj'),
                'Razão Social' => config('nfe.razao_social'),
            ];

            foreach ($config as $key => $value) {
                $this->line("  • {$key}: <comment>{$value}</comment>");
            }
            $this->newLine();

            // 2. Verificar certificado
            $this->info('2️⃣  Verificando certificado digital...');

            $certPath = config('nfe.cert_path');
            $certDisk = config('nfe.cert_disk');

            if (!\Storage::disk($certDisk)->exists($certPath)) {
                $this->error("  ✗ Certificado não encontrado em: {$certPath}");
                $this->warn("  ℹ️  Coloque o arquivo .pfx em storage/app/{$certPath}");
                return 1;
            }

            $pfxContent = \Storage::disk($certDisk)->get($certPath);
            $password = config('nfe.cert_password');

            try {
                $cert = \NFePHP\Common\Certificate::readPfx($pfxContent, $password);

                $this->line("  • CNPJ do Certificado: <comment>{$cert->getCnpj()}</comment>");
                $this->line("  • Válido de: <comment>{$cert->getValidFrom()->format('d/m/Y')}</comment>");
                $this->line("  • Válido até: <comment>{$cert->getValidTo()->format('d/m/Y')}</comment>");

                if ($cert->isExpired()) {
                    $this->error("  ✗ CERTIFICADO EXPIRADO!");
                    return 1;
                }

                $diasRestantes = now()->diffInDays($cert->getValidTo(), false);
                if ($diasRestantes < 30) {
                    $this->warn("  ⚠️  Certificado expira em {$diasRestantes} dias!");
                }

                $this->info("  ✓ Certificado válido");
            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao ler certificado: " . $e->getMessage());
                return 1;
            }

            $this->newLine();

            // 3. Testar conexão SEFAZ
            $this->info('3️⃣  Testando conexão com SEFAZ...');

            $status = $nfeService->consultarStatus();

            if ($status['online']) {
                $this->info("  ✓ SEFAZ ONLINE");
                $this->line("  • Código: <comment>{$status['code']}</comment>");
                $this->line("  • Mensagem: <comment>{$status['message']}</comment>");
            } else {
                $this->error("  ✗ SEFAZ OFFLINE ou com problemas");
                $this->line("  • Código: <comment>{$status['code']}</comment>");
                $this->line("  • Mensagem: <comment>{$status['message']}</comment>");
                $this->warn("  ℹ️  Consulte: http://www.nfe.fazenda.gov.br/portal/disponibilidade.aspx");
            }

            $this->newLine();
            $this->info('✅ Teste concluído com sucesso!');

            if (config('nfe.environment') === 2) {
                $this->warn('⚠️  Você está em ambiente de HOMOLOGAÇÃO');
            } else {
                $this->error('🔴 ATENÇÃO: Você está em ambiente de PRODUÇÃO!');
            }

            return 0;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Erro ao testar conexão:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->line('<comment>Stack trace:</comment>');
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}

