@extends('layouts.auth')

@section('title', 'Récupération du mot de passe')
@section('content')
<div class="popup-recup-mdp">
    <!-- Formes décoratives -->
    <div class="rectangle-6"></div>
    <div class="rectangle-7"></div>

    <!-- Carte principale -->
    <div class="rectangle-2623">
        <h2 class="text-xl font-semibold text-center text-gray-800 mb-6">
            🔑 Réinitialiser le mot de passe
        </h2>

        <p class="veuillez-renseigner-votre-identifiant-agent-pour-r-initialiser-votre-mot-de-passe">
            Veuillez renseigner votre identifiant <strong>(ANPT-...)</strong> pour réinitialiser votre mot de passe
        </p>

        <form method="POST" action="{{ route('password.identifiant.submit') }}" class="px-10 mt-10">
            @csrf
            <label for="identifiant" class="identifiant">Identifiant</label>
            <input type="text" id="identifiant" name="identifiant" class="rectangle-2" required>

            <button type="submit" class="rectangle-4 continuer">Continuer</button>
        </form>

        <a href="{{ route('login') }}" class="retour-la-page-de-connexion">
             Retour à la page de connexion
        </a>
    </div>
</div>
@endsection
