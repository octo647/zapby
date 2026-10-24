@extends('layouts.app')

@section('title', 'Perfil temporariamente indisponível - Zapby')

@section('meta')
    <meta name="robots" content="noindex,follow">
    <meta name="description" content="Este perfil está temporariamente indisponível no Zapby.">
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-lg shadow-md p-6 sm:p-8 text-center">
        <p class="text-sm font-semibold text-indigo-600 mb-2">Zapby</p>
        <h1 class="text-2xl font-bold text-gray-900 mb-3">Perfil temporariamente indisponível</h1>
        <p class="text-gray-700 mb-6">
            O agendamento por este link está pausado no momento. Tente novamente mais tarde.
        </p>
        <div class="flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('professional.login') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white transition hover:bg-indigo-700">
                Sou profissional: acessar minha conta
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-50">
                Ir para a página inicial
            </a>
        </div>
    </div>
</div>
@endsection
