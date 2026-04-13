@extends('layouts.dashboard')

@section('title', 'Paramètres')
@section('page-title', '⚙️ Paramètres du système')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {!! session('success') !!}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {!! session('error') !!}
    </div>
@endif



<div class="requests-card">
    <h2 class="chart-title">Gestion des paramètres</h2>

    <!-- Onglets (ancres) -->
    <div class="nav-tabs">
        <a class="nav-link" href="#users"> Utilisateurs</a>
        <a class="nav-link" href="#types"> Types de congés</a>
        <a class="nav-link" href="#directions">Directions</a>
    </div>

    <!-- Section Utilisateurs -->
    <section id="users" class="param-section">
        <h3>👥 Gestion des utilisateurs</h3>
      <form action="{{ route('users.create') }}" method="GET" style="display:inline;">
    <button type="submit" class="btn btn-success">
         Ajouter un utilisateur
    </button>
</form>
<!-- Bouton importer -->
<form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" style="display:inline;">
    @csrf
    <input type="file" name="file" accept=".csv,.xlsx" required>
    <button type="submit" class="btn btn-success">
         Importer utilisateurs
    </button>
</form>

        
<form method="GET" action="{{ route('settings.index') }}" class="mb-3" style="display:flex; gap:10px;">
    <input type="text" name="search" class="form-control" placeholder=" Rechercher par prénom ou nom" 
           value="{{ request('search') }}">
    <button type="submit" class="btn btn-primary">Filtrer</button>
</form>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Rôle</th>
                        <th>Direction</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->prenom }}</td>
                        <td>{{ $user->nom }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->direction->nom ?? '-' }}</td>
                        <td class="action-links">
                            <!-- Bouton ouvre un modal -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}">
                                ✏️
                            </button>

                         <div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Modifier {{ $user->prenom }} {{ $user->nom }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <!-- Prénom -->
            <div class="form-group mb-2">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control" 
                       value="{{ old('prenom', $user->prenom) }}" required>
            </div>

            <!-- Nom -->
            <div class="form-group mb-2">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" 
                       value="{{ old('nom', $user->nom) }}" required>
            </div>

            <!-- Sexe -->
            <div class="form-group mb-2">
                <label>Sexe</label>
                <select name="sexe" class="form-control" required>
                    <option value="H" {{ $user->sexe === 'H' ? 'selected' : '' }}>Homme</option>
                    <option value="F" {{ $user->sexe === 'F' ? 'selected' : '' }}>Femme</option>
                </select>
            </div>

            <!-- Rôle -->
            <div class="form-group mb-2">
                <label>Rôle</label>
                <select name="role" class="form-control" required>
    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
    <option value="rh" {{ $user->role === 'rh' ? 'selected' : '' }}>RH</option>
    <option value="responsable" {{ $user->role === 'responsable' ? 'selected' : '' }}>Responsable</option>
    <option value="agent" {{ $user->role === 'agent' ? 'selected' : '' }}>Agent</option>
    <option value="dg" {{ $user->role === 'dg' ? 'selected' : '' }}>DG</option>
</select>

            </div>

            <!-- Direction -->
            <div class="form-group mb-2">
                <label>Direction</label>
                <select name="direction_id" class="form-control">
                    <option value="">-- Aucune --</option>
                    @foreach($directions as $direction)
                        <option value="{{ $direction->id }}" 
                            {{ $user->direction_id == $direction->id ? 'selected' : '' }}>
                            {{ $direction->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Mot de passe -->
            <div class="form-group mb-2">
                <label>Nouveau mot de passe <small>(laisser vide si inchangé)</small></label>
                <input type="password" name="password" class="form-control">
            </div>

            <!-- Confirmation mot de passe -->
            <div class="form-group mb-2">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary mt-3">💾 Enregistrer</button>
        </form>
      </div>

    </div>
  </div>
</div>

                            <!-- Supprimer -->
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-icon" onclick="return confirm('Supprimer cet utilisateur ?')">
                             🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="5">Aucun utilisateur enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

   {{-- Section Types & Directions — modals d'édition inclus --}}

    <!-- TYPES -->
   <section id="types" class="param-section">
    <h3> Types de congés</h3>
                <form action="{{ route('types.store') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input name="nom" class="form-control form-control-sm" placeholder="Nom du type" required>
                    <input name="jours_alloues" type="number" class="form-control form-control-sm" placeholder="Jours/an" required min="1">
                    <button class="btn btn-success btn-sm" type="submit">➕</button>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Jours / an</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($types as $type)
                            <tr>
                                <td>{{ $type->nom }}</td>
                                <td>{{ $type->jours_alloues }}</td>
                                <td class="text-end">
                                    <!-- Edit button: opens modal -->
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editType{{ $type->id }}">
                                        ✏️
                                    </button>

                                    <!-- Modal Edit Type -->
                                    <div class="modal fade" id="editType{{ $type->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('types.update', $type->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Modifier le type</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-2">
                                                            <label class="form-label">Nom</label>
                                                            <input name="nom" class="form-control" value="{{ old('nom', $type->nom) }}" required>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Jours / an</label>
                                                            <input name="jours_alloues" type="number" class="form-control" min="1" value="{{ old('jours_alloues', $type->jours_alloues) }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete -->
                                    <form action="{{ route('types.destroy', $type->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Supprimer ce type ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3">Aucun type défini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
           </section>

    <!-- DIRECTIONS -->
    <section id="directions" class="param-section">
    <h3>Directions</h3>
                <form action="{{ route('directions.store') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input name="nom" class="form-control form-control-sm" placeholder="Nom direction" required>
                    <button class="btn btn-success btn-sm" type="submit">➕</button>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($directions as $dir)
                            <tr>
                                <td>{{ $dir->nom }}</td>
                                <td class="text-end">
                                    <!-- Edit -->
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editDirection{{ $dir->id }}">
                                        ✏️
                                    </button>

                                    <div class="modal fade" id="editDirection{{ $dir->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('directions.update', $dir->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Modifier la direction</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-2">
                                                            <label class="form-label">Nom</label>
                                                            <input name="nom" class="form-control" value="{{ old('nom', $dir->nom) }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete -->
                                    <form action="{{ route('directions.destroy', $dir->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Supprimer cette direction ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2">Aucune direction définie.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
           </section>

</div>


<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@endsection
