@extends('layouts.auth')

@section('title', 'Nouveau mot de passe')
@section('content')
<div class="page-de-recuperation-mot-de-passe">
    <!-- Formes décoratives -->
    <div class="rectangle-6"></div>
    <div class="rectangle-7"></div>
    <div class="rectangle-8"></div>
    <div class="rectangle-72"></div>

    <!-- Carte -->
    <div class="text-on-a-path p-10">
        <h2 class="text-xl font-semibold text-gray-800 text-center mb-6">
            🔒 Définir un nouveau mot de passe
        </h2>

        <form method="POST" action="{{ route('password.reset.submit') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="identifiant" value="{{ $identifiant }}">

            <div class="mb-4">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" required class="rectangle-2 w-full">
            </div>

            <div class="mb-4">
                <label for="password_confirmation">Confirmer</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="rectangle-2 w-full">
            </div>

            <div class="flex justify-between mt-8">
                <a href="{{ route('login') }}" class="rectangle-4 annuler text-center">Annuler</a>
                <button type="submit" class="rectangle-10 enregistrer">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
