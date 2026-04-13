@extends('layouts.dashboard')

@section('title', 'Historique des demandes')
@section('page-title', ' Historique des demandes')

@section('content')
<div class="requests-card">
    <h2 class="chart-title"> Historique des demandes</h2>



    <div class="table-responsive">
        <table class="table-auto w-full border">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Direction</th>
                    <th>Type</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Lieu</th>
                    <th>Statut</th>
                    <th>Pièce jointe</th>
                    <th>Autorisation</th>
                </tr>
            </thead>
            <tbody>
            @forelse($demandes as $demande)
                <tr>
                    <td>{{ $demande->user->prenom }} {{ $demande->user->nom }}</td>
                    <td>{{ $demande->user->direction->nom ?? 'N/A' }}</td>
                    <td>{{ $demande->type->nom ?? 'N/A' }}</td>
                    <td>{{ $demande->date_debut }}</td>
                    <td>{{ $demande->date_fin }}</td>
                    <td>{{ $demande->lieu ?? '-' }}</td>
                    <td>
                        <span class="status-badge
                            {{ $demande->statut === 'approuvé_dg' ? 'status-approved' :
                               ($demande->statut === 'rejeté' ? 'status-rejected' : 'status-pending') }}">
                            {{ ucfirst(str_replace('_', ' ', $demande->statut)) }}
                        </span>
                    </td>
                    <td>
                        @if($demande->piece_jointe)
                            <a href="{{ asset('storage/'.$demande->piece_jointe) }}" target="_blank" class="btn btn-sm btn-secondary">📎 Voir</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($demande->autorisation_signee_path)
                            <a href="{{ route('demandes.autorisation.telecharger', $demande->id) }}" class="btn btn-sm btn-success">⬇ Télécharger</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-gray-500">Aucune demande approuvée ou rejetée trouvée.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $demandes->links() }}
    </div>
</div>
@endsection
