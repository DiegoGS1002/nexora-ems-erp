<?php

namespace App\Ai\Tools;

use Illuminate\Support\Facades\File;

/**
 * Tool: Busca de código-fonte para localizar onde um erro pode estar sendo gerado.
 */
class BuscarCodigoFonteTool extends BaseTool
{
    public function name(): string
    {
        return 'buscar_codigo_fonte';
    }

    public function description(): string
    {
        return 'Pesquisa no código-fonte do Nexora ERP por termos técnicos, mensagens de erro ou nomes de função. '
            . 'Use quando o usuário perguntar onde um erro acontece no sistema, ou quando for preciso localizar o ponto de origem no código.';
    }

    public function parameters(): array
    {
        return [
            'consulta' => [
                'type'        => 'string',
                'description' => 'Texto ou palavra-chave para buscar (ex: "CFOP inválido", "NfeValidator", "sefaz_message").',
            ],
            'caminho' => [
                'type'        => 'string',
                'description' => 'Subpasta opcional para restringir a busca (ex: "app/Services").',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de ocorrências a retornar (padrão: 8, máximo: 15).',
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return ['consulta'];
    }

    public function execute(array $params, int $userId): array
    {
        $consulta = trim((string) ($params['consulta'] ?? ''));
        if ($consulta === '') {
            return ['erro' => 'Informe o termo de busca.'];
        }

        $limite  = min((int) ($params['limite'] ?? 8), 15);
        $base    = base_path();
        $pathOpt = trim((string) ($params['caminho'] ?? ''));

        $paths = $pathOpt !== ''
            ? [realpath($base . DIRECTORY_SEPARATOR . $pathOpt)]
            : [
                realpath($base . DIRECTORY_SEPARATOR . 'app'),
                realpath($base . DIRECTORY_SEPARATOR . 'routes'),
                realpath($base . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations'),
                realpath($base . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views'),
            ];

        $paths = array_values(array_filter($paths));
        if (empty($paths)) {
            return ['erro' => 'Caminho inválido para busca.'];
        }

        $matches = [];
        $needle  = mb_strtolower($consulta);

        foreach ($paths as $path) {
            foreach (File::allFiles($path) as $file) {
                $ext = strtolower($file->getExtension());
                if (! in_array($ext, ['php', 'blade.php', 'js', 'ts', 'vue', 'md'])) {
                    continue;
                }

                $content = File::get($file->getPathname());
                if (mb_stripos($content, $consulta) === false && mb_stripos($content, $needle) === false) {
                    continue;
                }

                $lines = preg_split('/\r\n|\n|\r/', $content);
                foreach ($lines as $i => $line) {
                    if (mb_stripos($line, $consulta) !== false || mb_stripos($line, $needle) !== false) {
                        $snippet = trim($line);
                        $matches[] = [
                            'arquivo' => str_replace($base . DIRECTORY_SEPARATOR, '', $file->getPathname()),
                            'linha'   => $i + 1,
                            'trecho'  => mb_strlen($snippet) > 220 ? mb_substr($snippet, 0, 220) . '…' : $snippet,
                        ];

                        if (count($matches) >= $limite) {
                            break 2;
                        }
                    }
                }
            }
        }

        if (empty($matches)) {
            return [
                'resultado'   => 'Nenhum trecho encontrado com o termo informado.',
                'sugestao'    => 'Tente buscar por parte da mensagem de erro ou o nome do campo/model.',
            ];
        }

        return [
            'resultado' => 'Trechos encontrados no código-fonte:',
            'total'     => count($matches),
            'ocorrencias' => $matches,
        ];
    }
}

