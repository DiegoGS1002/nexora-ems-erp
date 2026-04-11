#!/bin/bash

echo "🧹 LIMPEZA COMPLETA DO SISTEMA NEXORA ERP"
echo "=========================================="
echo ""

cd /home/dg/projects/nexora-ems-erp

echo "1️⃣ Limpando cache de views..."
php artisan view:clear
echo "✅ Cache de views limpo"
echo ""

echo "2️⃣ Limpando cache de rotas..."
php artisan route:clear
echo "✅ Cache de rotas limpo"
echo ""

echo "3️⃣ Limpando cache de configuração..."
php artisan config:clear
echo "✅ Cache de configuração limpo"
echo ""

echo "4️⃣ Limpando cache de aplicação..."
php artisan cache:clear 2>/dev/null || echo "⚠️  Cache de aplicação não limpo (banco desconectado, mas não é crítico)"
echo ""

echo "5️⃣ Removendo arquivos compiled..."
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/routes-v7.php 2>/dev/null || true
rm -f bootstrap/cache/events.php 2>/dev/null || true
echo "✅ Arquivos compiled removidos"
echo ""

echo "6️⃣ Verificando rotas fiscais..."
php artisan fiscal:verify
echo ""

echo "=========================================="
echo "✅ LIMPEZA COMPLETA!"
echo ""
echo "📌 PRÓXIMOS PASSOS:"
echo "1. Limpe o cache do seu navegador (Ctrl+Shift+R)"
echo "2. Ou abra em modo incógnito/privado"
echo "3. Acesse: /fiscal/tipos-operacao/create"
echo "4. Ou acesse: /fiscal/grupos-tributarios/create"
echo ""
echo "As páginas devem abrir normalmente! 🎉"

