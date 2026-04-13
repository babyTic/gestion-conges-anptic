@extends('layouts.dashboard')

@section('title', 'Tableau de bord')
@section('page-title', '')

@section('content')
<style>
    .dashboard-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-top: 30px;
        text-align: center;
    }

    .dashboard-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 25px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.15);
    }

    .dashboard-card img {
        width: 100px;
        height: 100px;
        object-fit: contain;
        margin-bottom: 15px;
        transition: transform 0.4s ease-in-out;
    }

    .dashboard-card:hover img {
        transform: scale(1.1) rotate(5deg);
    }

    .dashboard-card h3 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: #333;
    }

    .dashboard-card p {
        font-size: 0.95rem;
        color: #666;
    }

    /* Animation douce d’apparition */
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="text-center mt-4">
    <h2 class="text-primary">Bienvenue sur le tableau de bord ANPTIC</h2>
    <p class="text-muted">Accédez rapidement à vos principales actions</p>
</div>

<div class="dashboard-container">
    {{-- Nouvelle demande --}}
    <div class="dashboard-card fade-in" onclick="navigateTo('{{ route('demandes.create') }}')">
        <img src="{{ asset('assets/images/nouvelle_demande.gif') }}" alt="Nouvelle demande">
        <h3>Nouvelle demande</h3>
        <p>Soumettez une nouvelle demande de congé facilement.</p>
    </div>

    {{-- Plannifier ses congés --}}
    <div class="dashboard-card fade-in" style="animation-delay: 0.1s;" onclick="navigateTo('{{ route('conges.planification') }}')">
        <img src="{{ asset('assets/images/planning.gif') }}" alt="Plannifier congés">
        <h3>Plannifier ses congés</h3>
        <p>Visualisez et organisez vos périodes de congé.</p>
    </div>

    {{-- Historique des demandes --}}
    <div class="dashboard-card fade-in" style="animation-delay: 0.2s;" onclick="navigateTo('{{ route('demandes.index') }}')">
        <img src="{{ asset('assets/images/historique.gif') }}" alt="Historique">
        <h3>Historique</h3>
        <p>Consultez l’état de vos anciennes demandes.</p>
    </div>

    
</div>

<script>
    // ✅ Fonction JS pour rediriger proprement
    function navigateTo(url) {
        window.location.href = url;
    }
</script>
@endsection
