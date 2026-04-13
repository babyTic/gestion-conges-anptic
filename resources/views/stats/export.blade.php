@extends('layouts.dashboard')

@section('title', 'Statistiques - Export')
@section('page-title', '📤 Export de données')

@section('content')
<div class="requests-card">
    <h2 class="chart-title">Exporter les données</h2>
    <p>Choisis un format pour exporter les données des congés :</p>

    <div class="flex gap-3">
        <button class="btn-menu-like">📄 Exporter en PDF</button>
        <button class="btn-menu-like">📊 Exporter en Excel</button>
    </div>

    <!-- Bouton retour -->
    <form action="{{ route('stats.reports') }}" method="GET" style="margin-top: 20px;">
        <button type="submit" class="btn-secondary-like">← Revenir</button>
    </form>
</div>
@endsection
