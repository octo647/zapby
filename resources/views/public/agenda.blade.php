@extends('layouts.app')

@section('title', $professional->name . ' - Zapby')

@section('meta')
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Zapby">
    <meta property="og:title" content="Agende com {{ $professional->name }} pelo Zapby">
    <meta property="og:description" content="Toque para iniciar seu agendamento.">
    <meta property="og:url" content="{{ route('agenda.show', ['professional' => $professional->slug]) }}">
    <meta property="og:image" content="{{ url('/logo_fundo_branco.png') }}">
    <meta property="og:image:alt" content="Logo do Zapby">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Agende com {{ $professional->name }} pelo Zapby">
    <meta name="twitter:description" content="Toque para iniciar seu agendamento.">
    <meta name="twitter:image" content="{{ url('/logo_fundo_branco.png') }}">
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-lg shadow-md p-6 sm:p-8">
        <p class="text-sm font-semibold text-indigo-600 mb-2">Zapby</p>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $professional->name }}</h1>
        <p class="text-lg text-gray-700 mb-6">{{ $professional->profession }}</p>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-3 text-gray-700 mb-8">
            @if($professional->default_duration)
                <p>
                    <span class="font-medium text-gray-900">Duração padrão:</span>
                    {{ $professional->default_duration }} minutos
                </p>
            @endif

            <p>
                <span class="font-medium text-gray-900">Agendamento:</span>
                atendimento pelo WhatsApp
            </p>
        </div>

        <a
            href="{{ $whatsappUrl }}"
            target="_blank"
            rel="noopener"
            class="inline-flex w-full sm:w-auto items-center justify-center rounded-lg bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700"
        >
            Agendar pelo WhatsApp
        </a>
    </div>
</div>
@endsection
