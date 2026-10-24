<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendedores - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.dashboard') }}" class="text-green-600">&larr; Dashboard</a>
    <h1 class="text-2xl font-bold my-4">Vendedores e códigos de indicação</h1>

    @if(session('success'))<div class="bg-green-100 p-3 rounded mb-4">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="bg-red-100 p-3 rounded mb-4">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('admin.sellers.store') }}" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
        @csrf
        <input name="name" value="{{ old('name') }}" placeholder="Nome" required class="border rounded px-3 py-2">
        <input name="code" value="{{ old('code') }}" placeholder="Código (ex: JOAO10)" required class="border rounded px-3 py-2 uppercase">
        <input name="whatsapp" value="{{ old('whatsapp') }}" placeholder="WhatsApp" class="border rounded px-3 py-2">
        <button class="bg-green-600 text-white rounded px-4 py-2">Adicionar</button>
    </form>

    <table class="w-full bg-white rounded shadow text-left">
        <thead><tr class="border-b"><th class="p-3">Nome</th><th>Código</th><th>WhatsApp</th><th>Indicados</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach($sellers as $s)
            <tr class="border-b">
                <td class="p-3">{{ $s->name }}</td>
                <td class="font-mono">{{ $s->code }}</td>
                <td>{{ $s->whatsapp }}</td>
                <td>{{ $s->professionals_count }}</td>
                <td>{{ $s->active ? 'Ativo' : 'Inativo' }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.sellers.toggle', $s) }}">@csrf
                        <button class="text-sm text-blue-600">{{ $s->active ? 'Desativar' : 'Ativar' }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
