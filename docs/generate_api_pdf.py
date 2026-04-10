#!/usr/bin/env python3
"""
Script para gerar PDF da documentação da API
Usando apenas bibliotecas padrão do Python
"""

import subprocess
import sys
import os

def convert_md_to_html():
    """Converte o markdown para HTML com estilo"""

    md_content = open('API_ROUTES.md', 'r', encoding='utf-8').read()

    html_content = """<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rotas da API - Nexora EMS ERP</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 100%;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
            font-size: 28px;
            margin-top: 0;
        }

        h2 {
            color: #1e40af;
            margin-top: 30px;
            font-size: 22px;
            border-bottom: 2px solid #93c5fd;
            padding-bottom: 8px;
        }

        h3 {
            color: #1e3a8a;
            margin-top: 20px;
            font-size: 18px;
        }

        code {
            background-color: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #dc2626;
        }

        pre {
            background-color: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            line-height: 1.5;
        }

        pre code {
            background-color: transparent;
            color: #e2e8f0;
            padding: 0;
        }

        ul, ol {
            margin-left: 20px;
        }

        li {
            margin: 8px 0;
        }

        strong {
            color: #1e40af;
            font-weight: 600;
        }

        hr {
            border: none;
            border-top: 2px solid #e5e7eb;
            margin: 30px 0;
        }

        .endpoint {
            background-color: #eff6ff;
            padding: 10px;
            border-left: 4px solid #2563eb;
            margin: 10px 0;
            border-radius: 3px;
        }

        .method-get { color: #059669; font-weight: bold; }
        .method-post { color: #2563eb; font-weight: bold; }
        .method-put { color: #d97706; font-weight: bold; }
        .method-delete { color: #dc2626; font-weight: bold; }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #1e40af;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
"""

    # Processar o markdown manualmente (conversão básica)
    lines = md_content.split('\n')
    in_code_block = False
    code_lang = ''

    for line in lines:
        # Code blocks
        if line.startswith('```'):
            if not in_code_block:
                code_lang = line[3:].strip()
                html_content += '<pre><code>'
                in_code_block = True
            else:
                html_content += '</code></pre>\n'
                in_code_block = False
            continue

        if in_code_block:
            html_content += line.replace('<', '&lt;').replace('>', '&gt;') + '\n'
            continue

        # Headers
        if line.startswith('# '):
            html_content += f'<h1>{line[2:]}</h1>\n'
        elif line.startswith('## '):
            html_content += f'<h2>{line[3:]}</h2>\n'
        elif line.startswith('### '):
            html_content += f'<h3>{line[4:]}</h3>\n'
        # Horizontal rule
        elif line.strip() == '---':
            html_content += '<hr>\n'
        # Lists
        elif line.startswith('- '):
            if not lines[lines.index(line)-1].startswith('- '):
                html_content += '<ul>\n'
            html_content += f'<li>{process_inline_formatting(line[2:])}</li>\n'
            if lines.index(line) < len(lines)-1 and not lines[lines.index(line)+1].startswith('- '):
                html_content += '</ul>\n'
        # Bold text
        elif line.strip().startswith('**') and line.strip().endswith('**'):
            html_content += f'<p><strong>{line.strip()[2:-2]}</strong></p>\n'
        # Regular paragraphs
        elif line.strip():
            html_content += f'<p>{process_inline_formatting(line)}</p>\n'
        else:
            html_content += '\n'

    html_content += """
</body>
</html>
"""

    # Salvar HTML
    with open('API_ROUTES.html', 'w', encoding='utf-8') as f:
        f.write(html_content)

    print("✓ HTML gerado: API_ROUTES.html")
    return 'API_ROUTES.html'

def process_inline_formatting(text):
    """Processa formatação inline (bold, code, etc)"""
    import re

    # Code inline
    text = re.sub(r'`([^`]+)`', r'<code>\1</code>', text)

    # Bold
    text = re.sub(r'\*\*([^*]+)\*\*', r'<strong>\1</strong>', text)

    return text

def convert_html_to_pdf(html_file):
    """Tenta converter HTML para PDF usando ferramentas disponíveis"""

    pdf_file = 'API_ROUTES.pdf'

    # Tentar wkhtmltopdf
    try:
        result = subprocess.run(
            ['wkhtmltopdf', html_file, pdf_file],
            capture_output=True,
            text=True
        )
        if result.returncode == 0:
            print(f"✓ PDF gerado com wkhtmltopdf: {pdf_file}")
            return True
    except FileNotFoundError:
        pass

    # Tentar weasyprint
    try:
        result = subprocess.run(
            ['weasyprint', html_file, pdf_file],
            capture_output=True,
            text=True
        )
        if result.returncode == 0:
            print(f"✓ PDF gerado com weasyprint: {pdf_file}")
            return True
    except FileNotFoundError:
        pass

    # Tentar chromium/chrome headless
    for browser in ['chromium-browser', 'google-chrome', 'chromium']:
        try:
            result = subprocess.run(
                [browser, '--headless', '--disable-gpu', '--print-to-pdf=' + pdf_file, html_file],
                capture_output=True,
                text=True,
                timeout=30
            )
            if result.returncode == 0 and os.path.exists(pdf_file):
                print(f"✓ PDF gerado com {browser}: {pdf_file}")
                return True
        except (FileNotFoundError, subprocess.TimeoutExpired):
            pass

    print("⚠ Não foi possível gerar PDF automaticamente.")
    print(f"📄 HTML gerado: {html_file}")
    print("\nPara gerar o PDF manualmente, você pode:")
    print("1. Abrir o arquivo HTML no navegador e usar 'Imprimir > Salvar como PDF'")
    print("2. Instalar wkhtmltopdf: sudo apt install wkhtmltopdf")
    print("3. Instalar weasyprint: pip3 install weasyprint")

    return False

if __name__ == '__main__':
    os.chdir(os.path.dirname(os.path.abspath(__file__)))

    print("Gerando documentação PDF da API...\n")

    # Converter MD para HTML
    html_file = convert_md_to_html()

    # Tentar converter HTML para PDF
    convert_html_to_pdf(html_file)

    print("\n✓ Processo concluído!")

