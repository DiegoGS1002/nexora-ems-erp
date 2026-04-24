#!/bin/bash

# ============================================
# Script de Instalação - Agentes de IA
# Nexora EMS/ERP
# ============================================

set -e

echo ""
echo "🤖 ============================================"
echo "   Instalação dos Agentes de IA - Nexora ERP"
echo "   ============================================"
echo ""

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Função para imprimir com cores
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "ℹ $1"
}

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    print_error "Arquivo 'artisan' não encontrado. Execute este script na raiz do projeto Laravel."
    exit 1
fi

print_success "Diretório correto detectado"

# Verificar se o composer está instalado
if ! command -v composer &> /dev/null; then
    print_error "Composer não instalado. Instale em: https://getcomposer.org"
    exit 1
fi

print_success "Composer detectado"

# Verificar se PHP está instalado
if ! command -v php &> /dev/null; then
    print_error "PHP não instalado"
    exit 1
fi

print_success "PHP detectado: $(php -v | head -n 1)"

echo ""
print_info "Iniciando instalação..."
echo ""

# 1. Instalar dependência Gemini
echo "📦 Instalando pacote Google Gemini PHP..."
composer require google-gemini-php/laravel --quiet
print_success "Pacote instalado"

# 2. Verificar se .env.bak existe
if [ ! -f ".env" ]; then
    print_warning ".env não encontrado, copiando de .env.example..."
    cp .env.bakk.example .env.bakk
    print_success ".env criado"
fi

# 3. Verificar se GEMINI_API_KEY está configurada
if ! grep -q "GEMINI_API_KEY=" .env.bakk; then
    echo ""
    print_warning "GEMINI_API_KEY não encontrada no .env"
    echo ""
    echo "Você precisa obter uma API Key do Google Gemini:"
    echo "1. Acesse: https://aistudio.google.com/app/apikey"
    echo "2. Clique em 'Get API Key' ou 'Create API Key'"
    echo "3. Copie a chave gerada"
    echo ""
    read -p "Cole sua API Key aqui (ou pressione Enter para configurar depois): " api_key

    if [ ! -z "$api_key" ]; then
        echo "" >> .env.bakk
        echo "# Google Gemini API - Agentes de IA por Módulo" >> .env.bakk
        echo "GEMINI_API_KEY=$api_key" >> .env.bakk
        echo "GEMINI_MODEL=gemini-2.0-flash-exp" >> .env.bakk
        echo "GEMINI_TEMPERATURE=0.7" >> .env.bakk
        echo "GEMINI_MAX_TOKENS=2048" >> .env.bakk
        echo "GEMINI_REQUEST_TIMEOUT=60" >> .env.bakk
        print_success "API Key configurada no .env"
    else
        echo "" >> .env.bakk
        echo "# Google Gemini API - Agentes de IA por Módulo" >> .env.bakk
        echo "GEMINI_API_KEY=" >> .env.bakk
        echo "GEMINI_MODEL=gemini-2.0-flash-exp" >> .env.bakk
        echo "GEMINI_TEMPERATURE=0.7" >> .env.bakk
        echo "GEMINI_MAX_TOKENS=2048" >> .env.bakk
        echo "GEMINI_REQUEST_TIMEOUT=60" >> .env.bakk
        print_warning "Lembre-se de configurar GEMINI_API_KEY no .env antes de usar"
    fi
else
    print_success "GEMINI_API_KEY já configurada"
fi

# 4. Limpar caches
echo ""
print_info "Limpando caches..."
php artisan config:clear --quiet
php artisan cache:clear --quiet
print_success "Caches limpos"

# 5. Publicar assets do Livewire (se necessário)
echo ""
print_info "Publicando assets do Livewire..."
php artisan livewire:publish --assets --quiet || true
print_success "Assets publicados"

# 6. Verificar se os arquivos foram criados corretamente
echo ""
print_info "Verificando arquivos..."

files=(
    "app/Services/AiAssistantService.php"
    "app/Livewire/AiChatBubble.php"
    "app/Http/Middleware/DetectAiModule.php"
    "app/Console/Commands/TestAiAssistant.php"
    "config/gemini.php"
    "config/ai_contexts.php"
    "resources/views/livewire/ai-chat-bubble.blade.php"
)

all_files_ok=true
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        print_success "$file"
    else
        print_error "$file não encontrado"
        all_files_ok=false
    fi
done

if [ "$all_files_ok" = false ]; then
    echo ""
    print_error "Alguns arquivos estão faltando. Verifique a instalação."
    exit 1
fi

# 7. Testar conexão com API (se configurada)
echo ""
if grep -q "GEMINI_API_KEY=.\+" .env.bakk; then
    print_info "Testando conexão com Gemini API..."
    echo ""

    if php artisan ai:test financeiro --message="Olá" 2>/dev/null; then
        echo ""
        print_success "Conexão com Gemini API funcionando!"
    else
        echo ""
        print_warning "Não foi possível testar a API. Verifique a configuração."
    fi
else
    print_warning "API Key não configurada. Pule o teste."
fi

# 8. Exibir resumo
echo ""
echo "🎉 ============================================"
echo "   Instalação Concluída!"
echo "   ============================================"
echo ""
print_success "Sistema de Agentes de IA instalado com sucesso!"
echo ""
echo "📚 Próximos passos:"
echo ""
echo "1. Configure a API Key no .env (se ainda não fez):"
echo "   GEMINI_API_KEY=sua-chave-aqui"
echo ""
echo "2. Teste via CLI:"
echo "   php artisan ai:test financeiro"
echo ""
echo "3. Acesse o sistema e teste o chat flutuante"
echo ""
echo "4. Leia a documentação completa:"
echo "   - docs/INICIO_RAPIDO_IA.md (Guia de 5 min)"
echo "   - docs/AGENTES_IA_MODULOS.md (Doc completa)"
echo "   - docs/EXEMPLOS_INTEGRACAO_IA.md (Exemplos práticos)"
echo ""
echo "📞 Suporte:"
echo "   https://aistudio.google.com (API Key)"
echo "   docs/ (Documentação)"
echo ""
print_success "Pronto para uso! 🚀"
echo ""

