@extends('layouts.dashboard')

@section('title', 'Nouvelle demande de congé')
@section('page-title', ' Nouvelle demande de congé')

@section('content')
<div class="requests-card">
<div class="max-w-lg mx-auto bg-white p-6 rounded-xl shadow-md">

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <strong>⚠️ Erreurs :</strong>
            <ul class="mt-2 text-sm">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
@endif

    <form method="POST" action="{{ route('demandes.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- Type de congé -->
        <label class="block">
            <span class="text-gray-700">Type de congé</span>
            <select name="type_conge_id" class="border rounded-lg p-2 w-full" required>
                <option value="">-- Sélectionner --</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('type_conge_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->nom }}
                    </option>
                @endforeach
            </select>
        </label>

        <!-- Date début -->
        <input type="date" name="date_debut" value="{{ old('date_debut') }}" class="border rounded-lg p-2 w-full" required>

        <!-- Date fin -->
        <input type="date" name="date_fin" value="{{ old('date_fin') }}" class="border rounded-lg p-2 w-full" required>

        <!-- Lieu -->
        <input type="text" name="lieu" placeholder="Lieu du congé" value="{{ old('lieu') }}" class="border rounded-lg p-2 w-full" required>

        <!-- Intérimaire (affiché seulement si utilisateur != agent) -->
        @if(auth()->user()->role !== 'agent')
            <label class="block">
                <span class="text-gray-700">Intérimaire</span>
                <select name="interimaire_id" class="border rounded-lg p-2 w-full">
                    <option value="">-- Aucun intérimaire --</option>
                    @php
    $agents = \App\Models\User::where('role', 'agent')
        ->where('direction_id', auth()->user()->direction_id)
        ->get();
@endphp

@foreach($agents as $agent)
    <option value="{{ $agent->id }}" {{ old('interimaire_id') == $agent->id ? 'selected' : '' }}>
        {{ $agent->prenom }} {{ $agent->nom }}
    </option>
@endforeach

                </select>
            </label>
        @endif

        <!-- Motif -->
        <textarea name="motif" placeholder="Motif (optionnel)" class="border rounded-lg p-2 w-full">{{ old('motif') }}</textarea>

        <!-- Pièce jointe -->
        <label class="block">
            <span class="text-gray-700">Pièce jointe (PDF ou image)</span>
            <input type="file" name="piece_jointe" class="mt-1 block w-full border rounded-lg p-2">
        </label>

        <div class="flex justify-between">
            <a href="{{ route('demandes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                ← Annuler
            </a>
            <button type="submit" class="btn btn-primary">📤 Soumettre</button>
        </div>
    </form>
</div>
</div>
@endsection
