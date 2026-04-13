@extends('layouts.dashboard')

@section('content')
<div class="error-container">
    <h1>🚫 403 — Accès refusé</h1>
    <p>Désolé, vous n’avez pas la permission d’accéder à cette page.</p>
    <a href="{{ route('dashboard') }}" class="btn">Retour au tableau de bord</a>
</div>
@endsection
