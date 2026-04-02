<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Nexora ERP — Sistema de Gestão Empresarial">

    <title>@yield('title', 'Nexora ERP') | Nexora</title>

    {{-- Google Fonts — Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Fonts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

    {{-- Wrapper global --}}
    <div class="nx-app-wrapper" id="nx-app-wrapper">

        {{-- Sidebar Principal --}}
        @include('partials.navbar')

        {{-- Área principal --}}
        <div class="nx-page" id="nx-page">

            {{-- Alertas de sessão --}}
            @if(session()->has('success'))
                <div class="alert-success">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert-error">
                    ✕ {{ session('error') }}
                </div>
            @endif

            {{-- Conteúdo Principal --}}
            <main class="main-content">
                @yield('content')
            </main>

        </div>{{-- /.nx-page --}}

    </div>{{-- /.nx-app-wrapper --}}

    @stack('scripts')
</body>
</html>
