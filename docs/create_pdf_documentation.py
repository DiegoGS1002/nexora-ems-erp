#!/usr/bin/env python3
"""
Script para gerar PDF da documentação da API usando apenas bibliotecas padrão
Cria um PDF simplificado sem dependências externas
"""

import os
from datetime import datetime

def create_simple_pdf():
    """Cria um PDF simples com as rotas da API"""

    # Ler o arquivo markdown
    with open('API_ROUTES.md', 'r', encoding='utf-8') as f:
        content = f.read()

    # Criar um HTML mais completo para conversão
    html_template = """<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação API - Nexora EMS ERP</title>
    <style>
        @page {
            size: A4;
            margin: 2cm 1.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            font-size: 11pt;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            color: #2563eb;
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .header .subtitle {
            color: #64748b;
            font-size: 12pt;
        }

        .header .date {
            color: #94a3b8;
            font-size: 10pt;
            margin-top: 5px;
        }

        h2 {
            color: #1e40af;
            font-size: 18pt;
            margin-top: 25px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #93c5fd;
            page-break-after: avoid;
        }

        h3 {
            color: #1e3a8a;
            font-size: 14pt;
            margin-top: 20px;
            margin-bottom: 10px;
            page-break-after: avoid;
        }

        .endpoint-box {
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .method {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10pt;
            display: inline-block;
            margin-right: 8px;
        }

        .method-get { background: #dcfce7; color: #16a34a; }
        .method-post { background: #dbeafe; color: #2563eb; }
        .method-put { background: #fed7aa; color: #ea580c; }
        .method-delete { background: #fee2e2; color: #dc2626; }

        .endpoint-url {
            font-family: 'Courier New', monospace;
            color: #334155;
            font-size: 11pt;
        }

        .params {
            margin: 12px 0;
            padding: 10px;
            background: white;
            border-radius: 4px;
        }

        .params h4 {
            color: #475569;
            font-size: 11pt;
            margin-bottom: 8px;
        }

        .params ul {
            list-style: none;
            margin-left: 0;
        }

        .params li {
            padding: 5px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .params li:last-child {
            border-bottom: none;
        }

        .param-name {
            font-weight: bold;
            color: #1e40af;
        }

        .param-required {
            color: #dc2626;
            font-size: 9pt;
            font-weight: bold;
        }

        .param-optional {
            color: #64748b;
            font-size: 9pt;
        }

        .param-desc {
            color: #475569;
            margin-left: 10px;
        }

        code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            color: #b91c1c;
        }

        pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            line-height: 1.5;
            page-break-inside: avoid;
            margin: 15px 0;
        }

        pre code {
            background: transparent;
            color: #e2e8f0;
            padding: 0;
        }

        hr {
            border: none;
            border-top: 2px solid #e5e7eb;
            margin: 30px 0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #94a3b8;
            font-size: 9pt;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }

            h2, h3 {
                page-break-after: avoid;
            }

            .endpoint-box, pre {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
"""

    # Adicionar cabeçalho
    html_template += f"""
    <div class="header">
        <h1>Documentação da API</h1>
        <div class="subtitle">Nexora EMS ERP - Sistema de Gestão Empresarial</div>
        <div class="date">Gerado em {datetime.now().strftime('%d de %B de %Y às %H:%M')}</div>
    </div>
"""

    # Processar o conteúdo markdown
    lines = content.split('\n')
    i = 0
    in_code_block = False
    skip_first_header = True

    while i < len(lines):
        line = lines[i]

        # Pular o primeiro cabeçalho H1 (já temos no header)
        if skip_first_header and line.startswith('# '):
            skip_first_header = False
            i += 1
            continue

        # Code blocks
        if line.startswith('```'):
            if not in_code_block:
                code_lang = line[3:].strip()
                html_template += '<pre><code>'
                in_code_block = True
            else:
                html_template += '</code></pre>\n'
                in_code_block = False
            i += 1
            continue

        if in_code_block:
            html_template += line.replace('<', '&lt;').replace('>', '&gt;') + '\n'
            i += 1
            continue

        # Processar endpoints especiais
        if line.startswith('### ') and (i + 2 < len(lines)) and lines[i + 1].startswith('```'):
            # É um endpoint
            title = line[4:]
            i += 2  # Pular o ```

            # Pegar a URL do endpoint
            endpoint_line = lines[i]
            parts = endpoint_line.split(' ', 1)
            method = parts[0] if len(parts) > 0 else ''
            url = parts[1] if len(parts) > 1 else ''

            method_class = f'method-{method.lower()}' if method else 'method-get'

            html_template += f'''
    <h3>{title}</h3>
    <div class="endpoint-box">
        <div>
            <span class="method {method_class}">{method}</span>
            <span class="endpoint-url">{url}</span>
        </div>
'''

            i += 2  # Pular ``` de fechamento

            # Verificar se tem parâmetros
            if i < len(lines) and lines[i].startswith('**Parâmetros:**'):
                html_template += '''
        <div class="params">
            <h4>Parâmetros:</h4>
            <ul>
'''
                i += 1
                while i < len(lines) and lines[i].startswith('- '):
                    param_line = lines[i][2:]

                    # Processar linha de parâmetro
                    if '(obrigatório)' in param_line:
                        param_parts = param_line.split(':', 1)
                        param_name = param_parts[0].replace('(obrigatório)', '').replace('`', '').strip()
                        param_desc = param_parts[1].strip() if len(param_parts) > 1 else ''

                        html_template += f'''
                <li>
                    <span class="param-name">{param_name}</span>
                    <span class="param-required">(obrigatório)</span>
                    <span class="param-desc">{param_desc}</span>
                </li>
'''
                    elif '(opcional)' in param_line:
                        param_parts = param_line.split(':', 1)
                        param_name = param_parts[0].replace('(opcional)', '').replace('`', '').strip()
                        param_desc = param_parts[1].strip() if len(param_parts) > 1 else ''

                        html_template += f'''
                <li>
                    <span class="param-name">{param_name}</span>
                    <span class="param-optional">(opcional)</span>
                    <span class="param-desc">{param_desc}</span>
                </li>
'''
                    i += 1

                html_template += '''
            </ul>
        </div>
'''

            html_template += '    </div>\n'
            continue

        # Headers normais
        if line.startswith('## '):
            html_template += f'<h2>{line[3:]}</h2>\n'
        elif line.startswith('### '):
            html_template += f'<h3>{line[4:]}</h3>\n'
        elif line.strip() == '---':
            html_template += '<hr>\n'
        elif line.strip():
            # Processar texto normal
            processed_line = line
            processed_line = processed_line.replace('**', '<strong>').replace('**', '</strong>')
            html_template += f'<p>{processed_line}</p>\n'

        i += 1

    # Adicionar rodapé
    html_template += f"""
    <div class="footer">
        <p>Nexora EMS ERP - Sistema de Gestão Empresarial</p>
        <p>Documento gerado automaticamente em {datetime.now().strftime('%d/%m/%Y')}</p>
    </div>
</body>
</html>
"""

    # Salvar HTML melhorado
    with open('API_ROUTES_PDF.html', 'w', encoding='utf-8') as f:
        f.write(html_template)

    print(f"✓ HTML para PDF criado: API_ROUTES_PDF.html")
    print("\n📄 Para converter para PDF, você pode:")
    print("1. Abrir o arquivo no navegador (Chrome/Firefox) e usar Ctrl+P > Salvar como PDF")
    print("2. Usar: wkhtmltopdf API_ROUTES_PDF.html API_ROUTES.pdf")
    print("3. Usar: weasyprint API_ROUTES_PDF.html API_ROUTES.pdf")

    return 'API_ROUTES_PDF.html'

if __name__ == '__main__':
    os.chdir(os.path.dirname(os.path.abspath(__file__)))

    print("=" * 60)
    print("Gerando Documentação PDF da API - Nexora EMS ERP")
    print("=" * 60)
    print()

    html_file = create_simple_pdf()

    print()
    print("✓ Processo concluído com sucesso!")
    print(f"✓ Arquivo HTML gerado: {html_file}")
    print()


