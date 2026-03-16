<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Sistema de controle')</title>
    <link rel="stylesheet" href="{{ app()->environment() === 'production' ? secure_asset('css/home.css') : asset('css/home.css') }}">
</head>
<body>

    @include('partials.navbar')

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <main class="main-content">
        @yield('content')
    </main>

</body>
</html>
