@extends('layouts.dashboard')

@section('title', 'Solde de congés')
@section('page-title', '📊 Solde de mes congés')

@section('content')
    <div class="requests-card">
        <h2 class="chart-title">Mon solde de congés</h2>

        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>Type de congé</th>
                    <th>Jours alloués</th>
                    <th>Jours utilisés</th>
                    <th>Jours restants</th>
                </tr>
                </thead>
                <tbody>
                @forelse($soldes as $solde)
                    <tr>
                        <td>{{ $solde->type->nom }}</td>
                        <td>{{ $solde->type->jours_alloues }}</td>
                        <td>{{ $solde->utilises }}</td>
                        <td>
                            <span class="status-badge {{ $solde->restants > 0 ? 'status-approved' : 'status-rejected' }}">
                                {{ $solde->restants }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">Aucun solde disponible.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
