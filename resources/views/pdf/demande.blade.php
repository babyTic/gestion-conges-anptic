<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de congé</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 40px; line-height: 1.6; }
        h1, p { margin: 0 0 10px 0; }
        .header { text-align: center; margin-bottom: 40px; }
        .signature { margin-top: 60px; text-align: right; }
    </style>
</head>
<body>
<div class="header">
    <h1>Agence Nationale de Promotion des TIC (ANPTIC)</h1>
    <p><strong>Demande de congé</strong></p>
    <hr>
</div>

<p><strong>À :</strong> Monsieur le Directeur Général</p>
<p><strong>Objet :</strong> Demande de congé</p>

<p>Je soussigné(e) <strong>{{ $demande->user->prenom }} {{ strtoupper($demande->user->nom) }}</strong>,</p>
<p>Agent de la direction <strong>{{ $demande->user->direction->nom ?? 'N/A' }}</strong>,</p>
<p>sollicite un <strong>{{ $demande->type->nom ?? 'Congé' }}</strong> du
    <strong>{{ \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y') }}</strong> au
    <strong>{{ \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y') }}</strong>.
</p>

@if($demande->motif)
    <p><strong>Motif :</strong> {{ $demande->motif }}</p>
@endif

<p>Je reste à votre disposition pour tout complément d’information.</p>

<div class="signature">
    <p>Fait à Ouagadougou, le {{ now()->format('d/m/Y') }}</p>
    <p>Signature : ______________________</p>
</div>
</body>
</html>
