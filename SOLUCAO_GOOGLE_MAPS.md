# SOLUÇÃO RÁPIDA: Google Maps não carrega na Roteirização

## ❌ Problema Identificado

O erro "Esta página não carregou o Google Maps corretamente" ocorre porque **as APIs do Google Maps não estão habilitadas** no seu projeto do Google Cloud.

Sua chave da API está configurada, mas falta habilitar as APIs necessárias.

## ✅ Solução (5 minutos)

### Passo 1: Acesse o Google Cloud Console

Clique aqui: **[Google Cloud Console - APIs](https://console.cloud.google.com/apis/library)**

### Passo 2: Habilite as 3 APIs necessárias

Pesquise e habilite cada uma destas APIs:

1. **Maps JavaScript API**
   - Pesquise por "Maps JavaScript API"
   - Clique em "ENABLE" (Ativar)

2. **Places API**
   - Pesquise por "Places API"
   - Clique em "ENABLE" (Ativar)

3. **Directions API**
   - Pesquise por "Directions API"
   - Clique em "ENABLE" (Ativar)

### Passo 3: Configure o Billing (se ainda não tiver)

O Google Maps requer billing configurado, mesmo para uso gratuito:

1. Acesse: **[Google Cloud Console - Billing](https://console.cloud.google.com/billing)**
2. Configure uma conta de cobrança
3. **Não se preocupe:** O plano gratuito oferece $200/mês de crédito
4. Isso é suficiente para ~28.500 carregamentos de mapa/mês

### Passo 4: Aguarde alguns minutos e teste

As APIs podem levar de 1 a 5 minutos para serem ativadas.

Depois, recarregue a página de roteirização: `/logistica/routing`

## 🔍 Verificar Configuração

Execute no terminal para verificar se está tudo OK:

```bash
php artisan maps:verify
```

## 📋 Checklist Completo

- [ ] Chave da API configurada no `.env`
- [ ] Maps JavaScript API habilitada
- [ ] Places API habilitada
- [ ] Directions API habilitada
- [ ] Billing configurado no Google Cloud
- [ ] Aguardou alguns minutos após habilitar
- [ ] Testou em `/logistica/routing`

## ⚠️ Troubleshooting

### Ainda mostra erro após habilitar as APIs

1. Aguarde 5 minutos (as APIs levam tempo para ativar)
2. Limpe o cache: `php artisan config:clear`
3. Recarregue a página com Ctrl+F5

### Erro: "RefererNotAllowedMapError"

Sua chave tem restrições de domínio:

1. Acesse: [Credentials](https://console.cloud.google.com/apis/credentials)
2. Clique na sua chave
3. Em "Application restrictions", adicione:
   - `http://localhost:*`
   - `http://127.0.0.1:*`
   - Seu domínio de produção

### Erro: "ApiNotActivatedMapError"

A Maps JavaScript API não está habilitada:

1. Acesse: [Maps JavaScript API](https://console.cloud.google.com/apis/library/maps-backend.googleapis.com)
2. Clique em "ENABLE"

## 📞 Suporte

Se o problema persistir após seguir todos os passos:

1. Verifique o console do navegador (F12) para mensagens de erro específicas
2. Execute `php artisan maps:verify` e compartilhe o resultado
3. Verifique se está usando o projeto correto no Google Cloud Console

## 🔗 Links Úteis

- [Google Cloud Console](https://console.cloud.google.com/)
- [Habilitar APIs](https://console.cloud.google.com/apis/library)
- [Gerenciar Chaves](https://console.cloud.google.com/apis/credentials)
- [Configurar Billing](https://console.cloud.google.com/billing)
- [Documentação Completa](./GOOGLE_MAPS_SETUP.md)

