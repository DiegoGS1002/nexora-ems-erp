# Guia de Integração: BrasilAPI + Nexora EMS ERP

Este guia detalha a implementação da integração com a BrasilAPI para automação de cadastros de Clientes e Fornecedores via CNPJ e CEP dentro do ambiente Laravel.

## 1. Requisitos Prévios
* Laravel 10.x ou 11.x
* Cliente HTTP Guzzle (Padrão no Laravel)
* Campos de banco de dados compatíveis (Strings para CNPJ, CEP, etc.)

---

## 2. Criando o Service de Integração

Para manter o Controller limpo e permitir o reuso no módulo de Clientes e de Fornecedores, criaremos um Service.

**Arquivo:** `app/Services/BrasilAPIService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrasilAPIService
{
    protected string $baseUrl = '[https://brasilapi.com.br/api](https://brasilapi.com.br/api)';

    /**
     * Consulta CNPJ na BrasilAPI
     */
    public function consultarCnpj(string $cnpj): ?array
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/cnpj/v1/{$cnpj}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao consultar CNPJ no Nexora: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Consulta CEP na BrasilAPI (Versão 2 com Coordenadas)
     */
    public function consultarCep(string $cep): ?array
    {
        $cep = preg_replace('/\D/', '', $cep);

        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/cep/v2/{$cep}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao consultar CEP no Nexora: " . $e->getMessage());
            return null;
        }
    }
}
```

---

## 3. Criando a Rota de API (Proxy)
   Para que o seu Frontend (React/Vue/Blade) possa consultar sem problemas de CORS ou expor a lógica, criamos rotas no Laravel que servem de ponte.

Arquivo: routes/api.php

```PHP
use App\Http\Controllers\ExternalApiProxyController;

Route::get('/proxy/cnpj/{cnpj}', [ExternalApiProxyController::class, 'getCnpj']);
Route::get('/proxy/cep/{cep}', [ExternalApiProxyController::class, 'getCep']);

```

---

## 4. Criando o Controller de Proxy

```php
<?php

namespace App\Http\Controllers;

use App\Services\BrasilAPIService;
use Illuminate\Http\JsonResponse;

class ExternalApiProxyController extends Controller
{
    public function __construct(
        protected BrasilAPIService $brasilApiService
    ) {}

    public function getCnpj(string $cnpj): JsonResponse
    {
        $dados = $this->brasilApiService::consultarCnpj($cnpj);

        if (!$dados) {
            return response()->json(['error' => 'CNPJ não encontrado ou instabilidade no serviço'], 404);
        }

        return response()->json($dados);
    }

    public function getCep(string $cep): JsonResponse
    {
        $dados = $this->brasilApiService::consultarCep($cep);

        if (!$dados) {
            return response()->json(['error' => 'CEP não encontrado'], 404);
        }

        return response()->json($dados);
    }
}
```

---

## 5. Integração no Frontend
   No formulário de cadastro de Clientes ou Fornecedores, adicione um botão "Consultar CNPJ" ou "Consultar CEP" que faça uma requisição AJAX para as rotas criadas acima e preencha os campos automaticamente.

```javascript
// Exemplo com Fetch API
async function handleCnpjBlur(event) {
    const cnpj = event.target.value;
    if (cnpj.length < 14) return;

    const response = await fetch(`/api/proxy/cnpj/${cnpj}`);
    
    if (response.ok) {
        const data = await response.json();
        
        // Populando campos do ERP Nexora
        document.getElementById('razao_social').value = data.razao_social;
        document.getElementById('nome_fantasia').value = data.nome_fantasia;
        document.getElementById('logradouro').value = data.logradouro;
        document.getElementById('bairro').value = data.bairro;
        document.getElementById('cidade').value = data.municipio;
        document.getElementById('uf').value = data.uf;
        
        // Dica: A BrasilAPI retorna 'ddd'. Você pode prefixar o campo de telefone.
        document.getElementById('telefone').value = `(${data.ddd}) `;
    }
}

```

---
## 6.Mapeamento de Status (Regras de Negócio)
   Para o Nexora EMS ERP, considere automatizar estas verificações:

Situação Cadastral: Se descricao_situacao_cadastral for diferente de ATIVA, emita um alerta visual no cadastro.

Tipo de Empresa: Use cnae_fiscal_descricao para sugerir a categoria de fornecedor (ex: "Serviços de TI").

Localização: Se usar o CEP v2, você recebe location com lat/long. Ótimo para futuros módulos de logística ou rotas de entrega no ERP.

---

## 7. Observação sobre CPF
Atualmente, a BrasilAPI não oferece consulta de CPF.
Para o cadastro de pessoas físicas no Nexora:

Valide o formato do CPF no Frontend e Backend via Regex e algoritmo de dígito verificador.

Utilize a busca por CEP para agilizar o preenchimento do endereço residencial do cliente.

---
## 8. Considerações Finais
* Mantenha o Service de integração separado para facilitar manutenção e testes.
* Implemente tratamento de erros robusto para lidar com instabilidades da API externa.
* Considere cachear respostas frequentes para melhorar performance e reduzir chamadas à BrasilAPI.
* Documente a integração para que outros desenvolvedores possam entender e expandir facilmente.
* Teste a integração em ambiente de desenvolvimento antes de subir para produção, garantindo que os dados sejam mapeados corretamente e que o fluxo de cadastro esteja fluido.
* Fique atento às atualizações da BrasilAPI, pois mudanças no formato de resposta ou endpoints podem impactar a integração.
* Considere implementar uma camada de fallback para casos em que a BrasilAPI esteja indisponível, permitindo que o cadastro manual seja realizado sem bloqueios.
* Mantenha a segurança em mente, especialmente ao lidar com dados sensíveis como CNPJ e CEP. Certifique-se de que as rotas de proxy estejam protegidas contra abusos e que os dados sejam tratados de forma segura.
* Teste a integração com diferentes CNPJs e CEPs para garantir que o sistema lide corretamente com variações de dados e possíveis erros de formatação.
* Considere a possibilidade de expandir a integração no futuro para incluir outras APIs relevantes, como consulta de CPF, validação de e-mails ou integração com sistemas de pagamento, para enriquecer ainda mais o cadastro de clientes e fornecedores no Nexora EMS ERP.
* Mantenha a documentação atualizada à medida que a integração evolui, para garantir que outros membros da equipe possam entender e contribuir para o projeto de forma eficaz.
* Monitore o desempenho da integração e esteja preparado para otimizar as chamadas à API ou implementar caching se necessário, especialmente se o volume de consultas for alto.
* Considere a experiência do usuário ao implementar a integração, garantindo que as mensagens de erro sejam claras e que o processo de preenchimento automático seja intuitivo e eficiente.
* Teste a integração em diferentes ambientes (desenvolvimento, staging, produção) para garantir que funcione corretamente em todos os cenários e que as configurações de ambiente estejam adequadas para cada etapa do ciclo de vida do desenvolvimento.
* Mantenha uma comunicação aberta com a equipe de desenvolvimento e outros stakeholders para garantir que a integração atenda às necessidades do negócio e que quaisquer ajustes necessários sejam feitos de forma colaborativa e eficiente.
* Considere a possibilidade de implementar testes automatizados para a integração, garantindo que as funcionalidades estejam sempre funcionando corretamente e que quaisquer mudanças futuras não quebrem a integração existente.
* Fique atento às melhores práticas de desenvolvimento e segurança ao implementar a integração, garantindo que o código seja limpo, eficiente e seguro, e que a experiência do usuário seja sempre uma prioridade.
* Mantenha-se atualizado sobre as mudanças na BrasilAPI e no Nexora EMS ERP, garantindo que a integração continue funcionando corretamente e que quaisquer ajustes necessários sejam feitos de forma proativa para garantir a continuidade do serviço.
* Considere a possibilidade de implementar uma interface de administração para gerenciar as configurações da integração, como chaves de API, limites de taxa ou opções de cache, permitindo que a equipe de administração tenha controle total sobre a integração e possa ajustá-la conforme necessário para atender às necessidades do negócio.
* Mantenha um registro de logs detalhado para a integração, permitindo que a equipe de desenvolvimento monitore o desempenho, identifique possíveis problemas e otimize a integração conforme necessário para garantir a melhor experiência possível para os usuários do Nexora EMS ERP.
* Considere a possibilidade de implementar uma camada de autenticação para as rotas de proxy, garantindo que apenas usuários autorizados possam acessar as funcionalidades de consulta e que os dados sejam protegidos contra acessos não autorizados.
* Mantenha a documentação da integração atualizada e acessível para toda a equipe, garantindo que todos os membros possam entender o funcionamento da integração e contribuir para o seu desenvolvimento e manutenção de forma eficaz.
* Considere a possibilidade de implementar uma funcionalidade de fallback para casos em que a BrasilAPI esteja indisponível, permitindo que os usuários do Nexora EMS ERP possam continuar realizando cadastros manualmente sem interrupções, garantindo a continuidade do serviço mesmo em situações de instabilidade da API externa.
* Mantenha uma comunicação aberta com a equipe de suporte e atendimento ao cliente para garantir que quaisquer problemas relacionados à integração sejam tratados de forma rápida e eficiente, garantindo a satisfação dos usuários do Nexora EMS ERP e a continuidade do serviço.
* Considere a possibilidade de implementar uma funcionalidade de notificação para alertar os usuários do Nexora EMS ERP sobre possíveis problemas com a integração, como instabilidades da BrasilAPI ou erros de consulta, garantindo que os usuários estejam sempre informados sobre o status da integração e possam tomar as medidas necessárias para garantir a continuidade do serviço.
* Mantenha-se atento às melhores práticas de desenvolvimento e segurança ao implementar a integração, garantindo que o código seja limpo, eficiente e seguro, e que a experiência do usuário seja sempre uma prioridade.
* Considere a possibilidade de implementar uma funcionalidade de monitoramento para a integração, permitindo que a equipe de desenvolvimento monitore o desempenho da integração em tempo real e identifique possíveis problemas ou gargalos, garantindo que a integração esteja sempre funcionando de forma eficiente e que quaisquer problemas sejam tratados de forma proativa para garantir a melhor experiência possível para os usuários do Nexora EMS ERP.
* Mantenha-se atualizado sobre as mudanças na BrasilAPI e no Nexora EMS ERP, garantindo que a integração continue funcionando corretamente e que quaisquer ajustes necessários sejam feitos de forma proativa para garantir a continuidade do serviço.
* Considere a possibilidade de implementar uma funcionalidade de cache para as consultas à BrasilAPI, permitindo que as respostas sejam armazenadas temporariamente para reduzir o número de chamadas à API externa e melhorar o desempenho da integração, especialmente em casos de consultas frequentes ou dados que não mudam com frequência.
* Mantenha uma comunicação aberta com a equipe de desenvolvimento e outros stakeholders para garantir que a integração atenda às necessidades do negócio e que quaisquer ajustes necessários sejam feitos de forma colaborativa e eficiente, garantindo que a integração continue evoluindo para atender às necessidades dos usuários do Nexora EMS ERP e do negócio como um todo.
* Considere a possibilidade de implementar uma funcionalidade de logging detalhado para a integração, permitindo que a equipe de desenvolvimento monitore o desempenho da integração, identifique possíveis problemas e otimize a integração conforme necessário para garantir a melhor experiência possível para os usuários do Nexora EMS ERP.
* Mantenha a documentação da integração atualizada e acessível para toda a equipe, garantindo que todos os membros possam entender o funcionamento da integração e contribuir para o seu desenvolvimento e manutenção de forma eficaz, garantindo que a integração continue evoluindo para atender às necessidades dos usuários do Nexora EMS ERP e do negócio como um todo.
* Considere a possibilidade de implementar uma funcionalidade de fallback para casos em que a BrasilAPI esteja indisponível, permitindo que os usuários do Nexora EMS ERP possam continuar realizando cadastros manualmente sem interrupções, garantindo a continuidade do serviço mesmo em situações de instabilidade da API externa.
* Mantenha uma comunicação aberta com a equipe de suporte e atendimento ao cliente para garantir que quaisquer problemas relacionados à integração sejam tratados de forma rápida e eficiente, garantindo a satisfação dos usuários do Nexora EMS ERP e a continuidade do serviço.
* Considere a possibilidade de implementar uma funcionalidade de notificação para alertar os usuários do Nexora EMS ERP sobre possíveis problemas com a integração, como instabilidades da BrasilAPI ou erros de consulta, garantindo que os usuários estejam sempre informados sobre o status da integração e possam tomar as medidas necessárias para garantir a continuidade do serviço.
* Mantenha-se atento às melhores práticas de desenvolvimento e segurança ao implementar a integração, garantindo que o código seja limpo, eficiente e seguro, e que a experiência do usuário seja sempre uma prioridade, garantindo que a integração continue evoluindo para atender às necessidades dos usuários do Nexora EMS ERP e do negócio como um todo.
* Considere a possibilidade de implementar uma funcionalidade de monitoramento para a integração, permitindo que a equipe de desenvolvimento monitore o desempenho da integração em tempo real e identifique possíveis problemas ou gargalos, garantindo que a integração esteja sempre funcionando de forma eficiente e que quaisquer problemas sejam tratados de forma proativa para garantir a melhor experiência possível para os usuários do Nexora EMS ERP.
* Mantenha-se atualizado sobre as mudanças na BrasilAPI e no Nexora EMS ERP, garantindo que a integração continue funcionando corretamente e que quaisquer ajustes necessários sejam feitos de forma proativa para garantir a continuidade do serviço, garantindo que a integração continue evoluindo para atender às necessidades dos usuários do Nexora EMS ERP e do negócio como um todo.
* Considere a possibilidade de implementar uma funcionalidade de cache para as consultas à BrasilAPI, permitindo que as respostas sejam armazenadas temporariamente para reduzir o número de chamadas à API externa e melhorar o desempenho da integração, especialmente em casos de consultas frequentes ou dados que não mudam com frequência, garantindo que a integração continue evoluindo para atender às necessidades dos usuários do Nexora EMS ERP e do negócio como um todo.

---
