# Como Gerar o PDF da Documentação da API

Este guia explica como gerar a documentação da API em formato PDF.

## Arquivos Disponíveis

- `API_ROUTES.md` - Documentação em Markdown (original)
- `API_ROUTES.html` - Documentação em HTML básico
- `API_ROUTES_PDF.html` - Documentação em HTML otimizado para PDF

## Métodos para Gerar PDF

### Método 1: Usando o Navegador (Recomendado)

Este é o método mais simples e não requer instalações adicionais:

1. Abra o arquivo `API_ROUTES_PDF.html` em qualquer navegador (Chrome, Firefox, Edge)
2. Pressione `Ctrl + P` (ou `Cmd + P` no Mac)
3. Selecione "Salvar como PDF" como destino
4. Clique em "Salvar" e escolha o local do arquivo

**Vantagens:**
- Não requer instalação de software adicional
- Mantém toda a formatação e cores
- Resultado profissional

### Método 2: Usando wkhtmltopdf

Se preferir automatizar o processo:

```bash
# Instalar wkhtmltopdf
sudo apt install wkhtmltopdf

# Gerar o PDF
cd /home/dg/projects/nexora-ems-erp/docs
wkhtmltopdf API_ROUTES_PDF.html API_ROUTES.pdf
```

### Método 3: Usando WeasyPrint

WeasyPrint oferece excelente suporte a CSS:

```bash
# Instalar WeasyPrint
pip3 install weasyprint

# Gerar o PDF
cd /home/dg/projects/nexora-ems-erp/docs
weasyprint API_ROUTES_PDF.html API_ROUTES.pdf
```

### Método 4: Usando Pandoc

Para quem prefere converter diretamente do Markdown:

```bash
# Instalar Pandoc
sudo apt install pandoc texlive-xetex

# Gerar o PDF
cd /home/dg/projects/nexora-ems-erp/docs
pandoc API_ROUTES.md -o API_ROUTES.pdf --pdf-engine=xelatex
```

## Scripts Disponíveis

### generate_api_pdf.py

Script que tenta detectar e usar automaticamente a melhor ferramenta disponível:

```bash
python3 generate_api_pdf.py
```

### create_pdf_documentation.py

Script que gera um HTML otimizado para conversão em PDF:

```bash
python3 create_pdf_documentation.py
```

## Notas

- O arquivo HTML gerado (`API_ROUTES_PDF.html`) já está otimizado para impressão
- Recomendamos usar margens de 2cm em todos os lados ao imprimir
- O documento está formatado para papel A4
- Todas as cores e estilos são otimizados para impressão

## Resultado Esperado

O PDF final conterá:

- ✅ Cabeçalho profissional com logo e data
- ✅ Índice de rotas organizadas por módulo
- ✅ Descrição detalhada de cada endpoint
- ✅ Parâmetros de requisição com tipos e validações
- ✅ Exemplos de resposta JSON
- ✅ Códigos HTTP e mensagens de erro
- ✅ Formatação com cores e estilos profissionais
- ✅ Rodapé com informações do sistema

## Suporte

Para qualquer dúvida ou problema na geração do PDF, consulte a documentação técnica ou entre em contato com a equipe de desenvolvimento.

