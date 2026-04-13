@extends('layouts.dashboard')

@section('title', 'Ajouter un utilisateur')
@section('page-title', '➕ Ajouter un utilisateur')

@section('content')
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

    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
        @csrf

        <!-- Nom -->
        <input type="text" 
               name="nom" 
               placeholder="Nom" 
               value="{{ old('nom') }}" 
               class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" 
               required>

        <!-- Prénom -->
        <input type="text" 
               name="prenom" 
               placeholder="Prénom" 
               value="{{ old('prenom') }}" 
               class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" 
               required>

        <!-- Sexe -->
        <select name="sexe" 
                class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" 
                required>
            <option value="">-- Sélectionner le sexe --</option>
            <option value="H" {{ old('sexe') == 'H' ? 'selected' : '' }}>Homme</option>
            <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Femme</option>
        </select>

        <!-- Matricule -->
        <input type="text" 
               name="identifiant" 
               placeholder="Matricule (ex: ANPTIC-001)" 
               value="{{ old('identifiant') }}" 
               class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200"
               required>

        <!-- Direction -->
        <select name="direction_id" 
                class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200">
            <option value="">-- Aucune direction --</option>
            @foreach($directions as $dir)
                <option value="{{ $dir->id }}" {{ old('direction_id') == $dir->id ? 'selected' : '' }}>
                    {{ $dir->nom }}
                </option>
            @endforeach
        </select>

        <!-- Rôle -->
        <select name="role" 
                class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" 
                required>
            <option value="">-- Choisir un rôle --</option>
            <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Agent</option>
            <option value="responsable" {{ old('role') == 'responsable' ? 'selected' : '' }}>Responsable</option>
            <option value="rh" {{ old('role') == 'rh' ? 'selected' : '' }}>RH</option>
            <option value="dg" {{ old('role') == 'dg' ? 'selected' : '' }}>DG</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>

        <!-- Mot de passe -->
        <input type="password" 
               name="password" 
               placeholder="Mot de passe" 
               class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" 
               required>

        <div class="flex justify-between">
            <a href="{{ route('settings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                ← Annuler
            </a>
            <button type="submit" class="btn btn-primary">Créer</button>
        </div>
    </form>
</div>
@endsection
