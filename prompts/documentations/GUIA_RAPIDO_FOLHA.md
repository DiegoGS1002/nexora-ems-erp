# 🚀 Guia Rápido: Folha de Pagamento

## Acesso Rápido
**URL:** `http://localhost:8000/payroll`  
**Menu:** RH → Folha de Pagamento

---

## 📖 Passo a Passo

### 1️⃣ Gerar Folhas do Mês

#### Opção A: Gerar Todas
```
1. Clique em "Gerar Todas as Folhas"
2. Confirme a competência (ex: Abril/2026)
3. Sistema cria folhas para todos os colaboradores ativos
```

#### Opção B: Gerar Individual
```
1. Localize o colaborador na tabela
2. Clique em "⋮" (menu de ações)
3. Selecione "Gerar Folha"
```

---

### 2️⃣ Adicionar Proventos e Descontos

```
1. Clique em "Ver Holerite" do colaborador
2. Clique no botão "+ Adicionar Verba"
3. Preencha:
   - Descrição: Ex: "Hora Extra 50%"
   - Tipo: Provento ou Desconto
   - Valor: Ex: 800,00
4. Clique em "Salvar"
5. Sistema recalcula automaticamente
```

#### Exemplos de Proventos
- Hora Extra 50%
- Hora Extra 100%
- Adicional Noturno
- Comissão de Vendas
- Bônus de Produtividade
- Adicional de Insalubridade

#### Exemplos de Descontos
- INSS
- IRRF
- Vale Transporte
- Vale Refeição
- Plano de Saúde
- Empréstimo Consignado

---

### 3️⃣ Fechar Folha

```
⚠️ Após fechar, não será possível editar as verbas

1. Clique em "⋮" (menu de ações)
2. Selecione "Fechar Folha"
3. Confirme a operação
4. Status muda para "Fechada" (laranja)
```

**Ou dentro do Holerite:**
```
1. Abra o holerite
2. Clique no botão "Fechar Folha" (laranja)
3. Confirme
```

---

### 4️⃣ Registrar Pagamento

```
1. Folha precisa estar "Fechada"
2. Clique em "⋮" (menu de ações)
3. Selecione "Marcar como Pago"
4. Informe a data do pagamento
5. Confirme
6. Status muda para "Paga" (verde)
```

---

### 5️⃣ Visualizar KPIs

Os cards no topo mostram automaticamente:

```
┌─────────────────┐
│ Total Proventos │ ← Soma de todos os proventos
│   R$ 142.500    │
└─────────────────┘

┌─────────────────┐
│ Total Descontos │ ← Soma de todos os descontos
│   R$ 38.200     │
└─────────────────┘

┌─────────────────┐
│ Líquido a Pagar │ ← Valor total a pagar
│   R$ 104.300    │
└─────────────────┘

┌─────────────────┐
│ Status Folhas   │ ← Quantidade por status
│ 5 Rascunho      │
│ 10 Fechadas     │
│ 15 Pagas        │
└─────────────────┘
```

---

### 6️⃣ Mudar Competência

```
1. Use o campo "Mês/Ano" no topo da tabela
2. Selecione o mês desejado (ex: 05/2026)
3. Sistema atualiza automaticamente
```

---

## 🎯 Ações Disponíveis por Status

### Status: Não Gerada
- ✅ Gerar Folha

### Status: Rascunho (Cinza)
- ✅ Ver Holerite
- ✅ Adicionar Verbas
- ✅ Editar Verbas
- ✅ Remover Verbas
- ✅ Fechar Folha
- ✅ Excluir Folha

### Status: Fechada (Laranja)
- ✅ Ver Holerite (somente leitura)
- ✅ Marcar como Pago
- ✅ Excluir Folha

### Status: Paga (Verde)
- ✅ Ver Holerite (somente leitura)
- ✅ Excluir Folha

---

## 💡 Dicas e Boas Práticas

### ✅ Sempre faça nesta ordem:
```
1. Gerar folhas no início do mês
2. Adicionar todas as verbas
3. Conferir os totais
4. Fechar as folhas
5. Marcar como pagas após o pagamento
```

### ⚠️ Atenção:
- Folhas **Fechadas** não podem ser editadas
- Folhas **Pagas** são apenas para consulta
- Exclua uma folha para recriá-la (se necessário)

### 🎨 Legenda de Cores:
- 🟢 **Verde (+)** = Proventos (ganhos)
- 🔴 **Vermelho (-)** = Descontos (perdas)
- 🔵 **Azul** = Líquido (valor final)

### 📊 Cálculo Automático:
```
Líquido = Salário Base + Proventos - Descontos
```

---

## ❓ FAQ - Perguntas Frequentes

### Como editar uma verba?
```
1. Abra o holerite (folha deve estar em Rascunho)
2. Clique no ícone ✏️ ao lado da verba
3. Modifique os dados
4. Salve
```

### Como remover uma verba?
```
1. Abra o holerite (folha deve estar em Rascunho)
2. Clique no ícone 🗑️ ao lado da verba
3. Confirme a remoção
```

### Posso editar uma folha fechada?
```
❌ Não. Para editar:
1. Exclua a folha fechada
2. Gere novamente
3. Adicione as verbas
4. Feche novamente
```

### Como ver o histórico de pagamentos?
```
1. Mude a competência para meses anteriores
2. Visualize as folhas pagas
3. Abra o holerite de cada colaborador
```

### Quanto tempo leva para gerar todas as folhas?
```
⚡ Instantâneo - O sistema gera em segundos
Exemplo: 50 colaboradores = ~2 segundos
```

---

## 🔧 Atalhos de Teclado

| Ação | Atalho |
|------|--------|
| Fechar Modal | `ESC` |
| Salvar Formulário | `Ctrl + Enter` |

---

## 📱 Mobile

A página é **100% responsiva**:
- ✅ Funciona em smartphones
- ✅ Funciona em tablets
- ✅ Touch-friendly
- ✅ Menus adaptados

---

## 🆘 Suporte

### Problemas Comuns

**Não consigo adicionar verbas**
- ✅ Verifique se a folha está em "Rascunho"
- ✅ Folhas fechadas não podem ser editadas

**Totais não batem**
- ✅ Sistema recalcula automaticamente
- ✅ Atualize a página (F5)

**Folha não aparece**
- ✅ Verifique a competência selecionada
- ✅ Gere a folha primeiro

---

## 📞 Contato

Para dúvidas ou sugestões:
- 📧 Email: suporte@nexora.com
- 💬 Chat: Sistema interno
- 📱 Telefone: (11) 9999-9999

---

**Versão:** 1.0.0  
**Última atualização:** 09/04/2026  
**Sistema:** Nexora ERP

