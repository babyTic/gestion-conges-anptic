<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat de cessation de service</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            margin: 40px 60px;
            line-height: 1.6;
        }

        .ministere {
            text-align: center;
            font-size: 12px;
            line-height: 1.3;
        }

        .ministere strong {
            font-size: 13px;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        .titre-doc {
            text-align: center;
            margin-top: 20px;
            text-decoration: underline;
            font-weight: bold;
            font-size: 16px;
        }

        .reference {
            margin-top: 25px;
            font-size: 13px;
        }

        .content {
            margin-top: 35px;
            text-align: justify;
            font-size: 14px;
        }

        .signature {
            margin-top: 80px;
            text-align: right;
        }

        .signature img {
            max-width: 180px;
            margin-top: 5px;
        }

        .footer {
            margin-top: 80px;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <!-- EN-TÊTE COMPLET -->
<table width="100%" style="margin-bottom: 10px;">
    <tr>
        <!-- GAUCHE : MINISTÈRE -->
        <td style="width: 33%; text-align: left; font-size: 12px; line-height: 1.3; font-weight: bold;">
            MINISTERE DU DEVELOPPEMENT DE<br>
            L’ECONOMIE NUMERIQUE ET DES POSTES<br>
            -=-=-=-=-=-=-=-<br>
            SECRETARIAT GENERAL<br>
            -=-=-=-=-=-=-=-<br>
            AGENCE NATIONALE DE PROMOTION<br>
            DES TIC<br>
            -=-=-=-=-=-=-=-
        </td>

        <!-- CENTRE : LOGO -->
        <td style="width: 34%; text-align: center;">
            <img src="{{ public_path('assets/images/anptic1.png') }}" style="width: 120px;">
        </td>

        <!-- DROITE : BURKINA FASO -->
        <td style="width: 33%; text-align: right; font-size: 12px; line-height: 1.3; font-weight: bold;">
            Burkina Faso<br>
            <span style="font-weight: normal; font-style: italic;">
                La Patrie ou la Mort, nous Vaincrons
            </span><br>
            -------------- 

            <div class="reference">
        Ouagadougou, le {{ now()->format('d/m/Y') }}<br>
        <strong>N°2025-____/MTDPCE/SG/ANPTIC/SG/DRH</strong>
    </div>

        </td>
    </tr>
</table>

<hr>

    <!-- Titre du document -->
    <div class="titre-doc">CERTIFICAT DE CESSATION DE SERVICE</div>

    <!-- Corps du document -->
    <div class="content">
        <p>
            Je soussigné, <strong>Secrétaire Général de l’Agence Nationale de Promotion des TIC</strong>,
            certifie que 
            <strong>{{ $demande->user->prenom }} {{ $demande->user->nom }}</strong>,
            matricule <strong>{{ $demande->user->identifiant ?? '_________' }}</strong>, 
            bénéficiaire d’un congé administratif accordé suivant la décision 
           <strong>{{ $decision}}</strong>.
        </p>

        <p>
            Est autorisé(e) à jouir de son congé annuel pour compter du
            <strong>{{ \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y') }}</strong>
            au
            <strong>{{ \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y') }}</strong>
            inclus.
            L’agent a cessé service le 
            <strong>{{ \Carbon\Carbon::parse($demande->date_debut)->subDay()->format('d/m/Y') }}</strong>.
        </p>

        <p>
            L’intéressé(e) reprendra service le 
            <strong>{{ \Carbon\Carbon::parse($demande->date_fin)->addDay()->format('d/m/Y') }}</strong>.
        </p>

        <p>
            En foi de quoi le présent certificat est établi pour servir et valoir ce que de droit.
        </p>
    </div>

    <!-- Signature -->
    <div class="signature">
    <p><strong>Le Directeur Général</strong></p>

    @if (!empty($signatureDataUrl))
        {{-- Cas 1 : signature base64 (auto / dernière signature) --}}
        <img src="{{ $signatureDataUrl }}" alt="Signature DG" style="width:140px;">

    @elseif (!empty($signaturePath))
        {{-- Cas 2 : signature depuis un fichier --}}
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($signaturePath)) }}"
             alt="Signature DG" style="width:140px;">

    @else
        {{-- Aucun cas --}}
        <p style="color:red;">Signature non disponible</p>
    @endif
</div>


    <!-- Ampliations -->
    <div class="footer">
        <strong>Ampliations :</strong><br>
        - DRH<br>
        - DI<br>
        - Intéressé(e)
    </div>

</body>
</html>
