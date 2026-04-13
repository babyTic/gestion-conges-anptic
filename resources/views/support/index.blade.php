@extends('layouts.dashboard')

@section('title', 'Support')
@section('page-title', '🆘 Support et Aide')

@section('content')
<div class="requests-card">
    <h2 class="chart-title">📩 Contactez le support</h2>
    <p>Si vous rencontrez un problème ou avez une question, utilisez ce formulaire :</p>

    <form action="{{ route('support.send') }}" method="POST" class="support-form">
        @csrf
        <div class="form-group">
            <label for="subject">Sujet</label>
            <input type="text" id="subject" name="subject" required class="form-control">
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="4" required class="form-control"></textarea>
        </div>

        <button type="submit" class="btn-menu-like">📤 Envoyer</button>
    </form>

    <h3 style="margin-top:20px;">📚 FAQ</h3>
    <details>
        <summary>Comment faire une demande de congé ?</summary>
        <p>Allez dans l’onglet <strong>Mes demandes</strong>, cliquez sur ➕ et remplissez le formulaire.</p>
    </details>
    <details>
        <summary>À qui s’adresser si mon congé n’est pas validé ?</summary>
        <p>Contactez votre <strong>Responsable</strong> ou le service <strong>RH</strong>.</p>
    </details>
</div>

<style>
.support-form .form-group {
    margin-bottom: 15px;
}
details {
    margin-top: 10px;
    background: #f9f9f9;
    padding: 8px;
    border-radius: 6px;
}
details summary {
    font-weight: bold;
    cursor: pointer;
}
</style>
@endsection
