<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class VerifyGoogleMapsKey extends Command
{
    protected $signature = 'maps:verify';
    protected $description = 'Verifica se a chave do Google Maps API está configurada corretamente';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  Verificação da Configuração do Google Maps API');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        $apiKey = config('services.google_maps.key');

        // 1. Verificar se a chave existe
        if (empty($apiKey)) {
            $this->error('❌ ERRO: Chave da API não configurada!');
            $this->newLine();
            $this->warn('Adicione a chave no arquivo .env.bak:');
            $this->line('   GOOGLE_MAPS_API_KEY=sua_chave_aqui');
            $this->newLine();
            $this->info('Para obter uma chave, acesse:');
            $this->line('   https://console.cloud.google.com/apis/credentials');
            $this->newLine();
            return 1;
        }

        $this->info('✓ Chave encontrada no .env.bak');

        // 2. Verificar formato da chave
        if (!str_starts_with($apiKey, 'AIzaSy')) {
            $this->warn('⚠ AVISO: A chave não começa com "AIzaSy"');
            $this->warn('  Formato esperado: AIzaSy...');
            $this->newLine();
        } else {
            $this->info('✓ Formato da chave parece correto');
        }

        $keyLength = strlen($apiKey);
        if ($keyLength < 35 || $keyLength > 45) {
            $this->warn("⚠ AVISO: Tamanho incomum da chave: {$keyLength} caracteres");
            $this->warn('  Tamanho esperado: ~39 caracteres');
            $this->newLine();
        } else {
            $this->info("✓ Tamanho da chave: {$keyLength} caracteres");
        }

        // 3. Testar a chave com uma requisição real
        $this->newLine();
        $this->info('Testando a chave com o Google Maps API...');

        try {
            // Teste com Geocoding API (simples e confiável)
            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => 'São Paulo, Brasil',
                'key' => $apiKey,
            ]);

            $data = $response->json();

            if (isset($data['status'])) {
                $status = $data['status'];

                if ($status === 'OK') {
                    $this->info('✓ Chave válida! A API está respondendo corretamente.');
                    $this->newLine();

                    $this->info('APIs que você deve habilitar no Google Cloud Console:');
                    $this->line('  1. Maps JavaScript API (obrigatória)');
                    $this->line('  2. Places API (para autocomplete)');
                    $this->line('  3. Directions API (para cálculo de rotas)');
                    $this->line('  4. Geocoding API (recomendada)');
                    $this->newLine();

                    $this->info('Acesse: https://console.cloud.google.com/apis/library');
                    $this->newLine();

                    return 0;

                } elseif ($status === 'REQUEST_DENIED') {
                    $this->error('❌ ERRO: Requisição negada!');
                    $this->newLine();

                    if (isset($data['error_message'])) {
                        $this->warn('Mensagem do Google: ' . $data['error_message']);
                        $this->newLine();
                    }

                    $this->warn('Possíveis causas:');
                    $this->line('  1. A chave tem restrições que bloqueiam esta requisição');
                    $this->line('  2. A API Geocoding não está habilitada');
                    $this->line('  3. Billing não está configurado no Google Cloud');
                    $this->newLine();

                } elseif ($status === 'INVALID_REQUEST') {
                    $this->warn('⚠ A chave parece válida, mas a requisição foi inválida');
                    $this->info('  Isso é esperado - a chave está funcionando!');
                    $this->newLine();

                } else {
                    $this->error("❌ Status inesperado: {$status}");

                    if (isset($data['error_message'])) {
                        $this->warn('Mensagem: ' . $data['error_message']);
                    }
                    $this->newLine();
                }
            } else {
                $this->error('❌ Resposta inválida do Google Maps API');
                $this->newLine();
            }

        } catch (\Exception $e) {
            $this->error('❌ Erro ao testar a chave: ' . $e->getMessage());
            $this->newLine();
        }

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  Documentação completa em:');
        $this->line('  docs/GOOGLE_MAPS_SETUP.md');
        $this->info('═══════════════════════════════════════════════════════');

        return 1;
    }
}

