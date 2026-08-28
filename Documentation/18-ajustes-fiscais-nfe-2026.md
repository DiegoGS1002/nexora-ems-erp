# Ajustes fiscais NF-e — problemas 3 a 6

## Escopo

Esta entrega amplia os fluxos de NF-e de saída e entrada conforme `prompts/prompt-ajusteNF.md`. As mudanças foram organizadas por ponto do diagnóstico e usam serviços de domínio, enums, persistência idempotente e testes Pest.

## 3. NF-e de saída

- **3.1 Identificação:** natureza, tipo de operação, destino calculado/configurado, consumidor, presença, finalidade, emissão/contingência, documento referenciado, intermediador e locais de retirada/entrega.
- **3.2 ICMS:** CST/CSOSN completos por meio de dados fiscais extensíveis, redução, diferimento, desoneração, ST, FCP, DIFAL, monofásico, crédito SN e validação CRT.
- **3.3 Produtos:** CEST, cBenef, EX TIPI, unidade tributável, GTIN, escala/fabricante, pedido, rastreabilidade e grupos de medicamentos, combustíveis, veículos, armas e importação.
- **3.4 Totais:** seguro, despesas, ST, FCP, desoneração, II, IPI devolvido, IBPT e conciliação obrigatória do total.
- **3.5 Transporte:** modalidade, transportador, veículo, volumes, pesos, lacres e retenção de ICMS.
- **3.6 Pagamento:** múltiplos meios, à vista/a prazo, sem pagamento, cartão, PIX, boleto, transferência, troco, fatura, duplicatas e conciliação.
- **3.7 Reforma:** IS, IBS UF/município, CBS, CST/cClassTrib, redução, diferimento, devolução, monofásico, créditos e totalizadores da NT 2025.002.

Os campos detalhados de ICMS, grupos especiais de produto e reforma são JSON estruturados com os mesmos nomes do leiaute NFePHP. Isso permite acompanhar novas Notas Técnicas sem criar dezenas de migrations a cada versão, mantendo campos centrais indexáveis em colunas próprias.

## 4. NF-e de entrada

- A rota `/fiscal/entrada` resolve diretamente o componente Livewire e possui teste de regressão.
- A Distribuição DF-e controla NSU por empresa/ambiente, descompacta e guarda resumos, documentos e eventos, com job único e janela antiabuso.
- Os quatro eventos de manifestação possuem protocolo, XML de resposta e idempotência.
- A importação valida parser seguro, schema, assinatura, modelo, chave/dígito, destinatário, protocolo, duplicidade e situação SEFAZ; original e hash são preservados.
- A conferência de compra compara vínculo/código, saldo, quantidade, preço e acessórios, persiste divergências e exige aprovação.
- A escrituração rateia despesas, registra tributos/créditos, cria estoque e parcelas financeiras conforme duplicatas.
- Cancelamento escriturado gera estornos de estoque, financeiro e pedido; títulos pagos bloqueiam reversão automática.

## 5. Operação e governança

- Inutilização, CC-e, cancelamento, consulta de recibo e conciliação de situação.
- Jobs únicos com tentativas e backoff para transmissão.
- Numeração por empresa, documento, série e ambiente.
- Policy para emissão, cancelamento e correção.
- Log fiscal append-only (`FiscalOperationLog`).
- Monitor de certificado e validade por empresa.
- Envio de XML e DANFE por e-mail.
- Disco de backup configurável e retenção legal por empresa.
- Importação CSV versionável: `ncm`, `cest`, `cbenef`, `payment_method` e `cclasstrib`.

Exemplo:

```bash
./vendor/bin/sail artisan fiscal:import-reference ncm storage/app/fiscal/ncm.csv
```

O CSV usa `;` e deve conter ao menos `code` e `description`.

## Configuração

Além das variáveis já existentes:

```dotenv
NFE_BACKUP_DISK=local
```

Em produção, configure `NFE_BACKUP_DISK` para armazenamento fisicamente separado e aplique política imutável/versionada no provedor.

## Filas e agendamento

O scheduler despacha:

- Distribuição DF-e a cada 15 minutos, respeitando `next_query_at` e `maxNSU`.
- Conciliação de notas enviadas/autorizadas a cada hora.

Mantenha scheduler e worker ativos:

```bash
./vendor/bin/sail artisan schedule:work
./vendor/bin/sail artisan queue:work --tries=5
```

## Verificação

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan test tests/Feature/NFeXmlTest.php
./vendor/bin/sail artisan test tests/Feature/NFeDistributionServiceTest.php
./vendor/bin/sail artisan test tests/Feature/RecipientManifestationServiceTest.php
./vendor/bin/sail artisan test tests/Feature/NFeServiceTest.php tests/Feature/FiscalPagesTest.php
./vendor/bin/sail pint
```

Nesta entrega os comandos não puderam ser executados porque Docker/Podman não estava iniciado. `git diff --check` foi usado como validação estática mínima; a execução dos comandos acima é obrigatória antes do deploy.

## Implantação

1. Faça backup do banco e dos XML existentes.
2. Inicie a infraestrutura Sail e execute migrations.
3. Configure worker, scheduler, certificado e disco de backup.
4. Importe as tabelas fiscais oficiais vigentes.
5. Execute a suíte fiscal completa.
6. Valide emissão e Distribuição DF-e em homologação antes de produção.
