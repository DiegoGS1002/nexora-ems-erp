@extends('layouts.app')

@section('title', 'Pagina inicial')

@section('content')
    <main class="welcome">
        <div class="welcome-message">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
    </main>
@endsection
