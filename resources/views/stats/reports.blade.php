@extends('layouts.dashboard')

@section('title', 'Statistiques - Rapports')
@section('page-title', '📑 Rapports')

@section('content')
<div class="requests-card">
    <h2 class="chart-title">Rapports et exportation</h2>
    <p>📊 Génère des rapports mensuels, trimestriels ou annuels.</p>

    <form action="{{ route('stats.export') }}" method="GET" style="margin-top:10px;">
        <button type="submit" class="btn-menu-like">📤 Exporter un rapport</button>
    </form>

    <!-- Bouton retour -->
    <form action="{{ route('stats.dashboard') }}" method="GET" style="margin-top: 20px;">
        <button type="submit" class="btn-secondary-like">← Revenir</button>
    </form>
</div>
@endsection
