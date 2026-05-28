# Módulo de Empresas (Multi-empresa)

## 1. Visão Geral

O módulo de Empresas permite gerenciar múltiplas filiais/CNPJs dentro do mesmo sistema, cada uma com suas configurações fiscais, certificados digitais e dados cadastrais.

### Funcionalidades Principais
- Cadastro de múltiplas empresas
- Configurações fiscais por empresa
- Certificado digital (A1) por empresa
- Empresa padrão do sistema
- Séries de NF-e por empresa
- Logomarca customizada

---

## 2. Estrutura Técnica

### 2.1 Model: `Company`

```php
protected $fillable = [
    'trade_name',
    'company_name',
    'cnpj',
    'state_registration',
    'municipal_registration',
    'email',
    'phone',
    
    // Endereço
    'zip_code',
    'street',
    'number',
    'city',
    'state',
    'ibge_code',
    
    // Fiscal
    'tax_regime', // simples_nacional, lucro_presumido, lucro_real
    'crt', // 1, 2, 3
    'nfe_series',
    'nfce_series',
    'nfe_environment', // homologacao, producao
    'certificate_path',
    'certificate_password',
    
    'logo_path',
    'is_default',
    'is_active',
];
```

### 2.2 Uso no Sistema

```php
// Empresa padrão
$company = Company::where('is_default', true)->first();

// Ou via helper
$company = app('current_company');

// Ou middleware InjectCompany
view()->share('company', $company);
```

### 2.3 Configuração por Empresa

```php
// NF-e usa configurações da empresa
$config = [
    'atualizacao' => date('Y-m-d h:i:s'),
    'tpAmb' => $company->nfe_environment === 'producao' ? 1 : 2,
    'razaosocial' => $company->company_name,
    'cnpj' => $company->cnpj,
    'siglaUF' => $company->state,
    'schemes' => 'PL_009_V4',
    'versao' => '4.00',
    'tokenIBPT' => 'AAAAAAA',
    'CSC' => $company->csc_code,
    'CSCid' => $company->csc_id,
];
```

---

## 3. Integrações

### 3.1 NF-e
Cada empresa usa seu próprio certificado e configurações para emissão.

### 3.2 Pedidos
Pedidos vinculam a empresa emissora (`company_branch`).

---

## 4. Exemplos

```php
$company = Company::create([
    'trade_name' => 'Nexora Matriz',
    'company_name' => 'Nexora Sistemas Ltda',
    'cnpj' => '12.345.678/0001-99',
    'state_registration' => '123456789',
    'tax_regime' => 'simples_nacional',
    'crt' => 1,
    'is_default' => true,
]);
```

---

**Caminho**: `app/Models/Company.php`, `app/Livewire/Administracao/Empresas/`

