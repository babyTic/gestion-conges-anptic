@extends('layouts.dashboard')

@section('page-title', 'Modifier un utilisateur')

@section('content')
<div class="requests-card">
    <h2>✏️ Modifier l’utilisateur</h2>

    <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nom complet</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label for="role">Rôle</label>
            <select name="role" id="role" required>
                <option value="agent" {{ $user->role === 'agent' ? 'selected' : '' }}>👤 Agent</option>
                <option value="responsable" {{ $user->role === 'responsable' ? 'selected' : '' }}>👨‍💼 Responsable</option>
                <option value="rh" {{ $user->role === 'rh' ? 'selected' : '' }}>👩‍💼 RH</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>⚙️ Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status">Statut</label>
            <select name="status" id="status">
                <option value="actif" {{ $user->status === 'actif' ? 'selected' : '' }}>✅ Actif</option>
                <option value="inactif" {{ $user->status === 'inactif' ? 'selected' : '' }}>🚫 Inactif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">↩️ Annuler</a>
    </form>
</div>
@endsection
