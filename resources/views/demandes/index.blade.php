@extends('layouts.dashboard')

@section('title', 'Mes demandes')
@section('page-title', ' Mes demandes')

@section('content')
<div class="requests-card">
    <h2 class="chart-title"> Liste de mes demandes</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Lieu</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($demandes as $demande)
                <tr>
                    <td>{{ $demande->type->nom ?? 'N/A' }}</td>
                    <td>{{ $demande->date_debut }}</td>
                    <td>{{ $demande->date_fin }}</td>
                    <td>{{ $demande->lieu }}</td>
                    <td>
                        <span class="status-badge 
                            {{ str_contains($demande->statut, 'approuve') ? 'status-approved' :
                               (str_contains($demande->statut, 'rejet') ? 'status-rejected' : 'status-pending') }}">
                            {{ ucfirst(str_replace('_', ' ', $demande->statut)) }}
                        </span>
                    </td>

                    <td class="flex gap-2">

                        {{-- ✅ Télécharger autorisation signée (visible uniquement après validation DG) --}}
                        @if($demande->statut === 'approuve_dg' 
                            && !empty($demande->autorisation_signee_path) )
                            <a href="{{ route('demandes.autorisation.telecharger', $demande->id) }}" 
                               class="btn btn-sm btn-primary">
                               📥 Autorisation
                            </a>
                        @endif


                        {{-- ✅ Confirmer retour (l’agent génère un certificat brouillon) --}}
                        @if(
                            now()->greaterThanOrEqualTo($demande->date_fin)
                            && in_array($demande->statut, ['approuve_dg', 'en_attente_signature_dg'])
                            && empty($demande->certificat_path)
                        )
                            <form action="{{ route('demandes.confirmerRetour', $demande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    ✅ Confirmer retour
                                </button>
                            </form>
                        @endif
{{-- 📥 Bouton Certificat signé --}}
@if($demande->statut === 'termine')
    <button type="button" 
        class="btn btn-success" 
        onclick="window.location.href='{{ route('telecharger.certificat', $demande->id) }}'">
    📄 Télécharger le certificat signé
</button>
@endif
                        {{-- ✅ Supprimer si encore en attente --}}
                      @if (in_array($demande->statut, ['soumis', 'rejete','termine' ]))

                            <form action="{{ route('demandes.destroy', $demande->id) }}" method="POST" 
                                  onsubmit="return confirm('Voulez-vous vraiment supprimer cette demande ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">🗑 Supprimer</button>
                            </form>
                        @endif

                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucune demande enregistrée.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
