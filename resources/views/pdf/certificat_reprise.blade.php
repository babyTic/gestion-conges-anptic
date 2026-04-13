<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat de reprise de service</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px 50px;
            font-size: 13.5px;
            line-height: 1.6;
        }

        .header-logos {
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
        }
        .header-logos img {
            height: 80px;
        }
        .header-logos .left { float: left; }
        .header-logos .center { display: inline-block; }
        .header-logos .right { float: right; }

        .header-text {
            text-align: center;
            margin-top: 110px;
            font-weight: bold;
        }

        hr { border: none; border-top: 1px solid #000; margin: 10px 0; }

        .reference {
            text-align: right;
            margin-top: 20px;
            font-size: 13px;
        }

        .title {
            text-align: center;
            text-decoration: underline;
            margin: 30px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .content { text-align: justify; margin-top: 15px; }

        .signature {
            margin-top: 70px;
            text-align: right;
        }

        .signature img {
            width: 140px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 40px;
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

    <!-- TITLE -->
    <div class="title">CERTIFICAT DE REPRISE DE SERVICE</div>

    <!-- CONTENT -->
    <div class="content">
        Je soussigné, <strong>Directeur Général de l’Agence Nationale de Promotion des Technologies
        de l’Information et de la Communication (ANPTIC)</strong>, certifie que 
        <strong>{{ $demande->user->sexe == 'F' ? 'Madame' : 'Monsieur' }} 
        {{ $demande->user->prenom }} {{ $demande->user->nom }}</strong>, matricule 
        <strong>{{ $demande->user->identifiant ?? '........' }}</strong>, bénéficiaire d’un 
        congé {{ strtolower($demande->type->nom) }} couvrant la période du 
        <strong>{{ \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y') }}</strong> 
        au <strong>{{ \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y') }}</strong> inclus,
        suivant la décision <strong>{{ $decision ?? '—' }}</strong>
, 
        a repris service le 
        <strong>{{ \Carbon\Carbon::parse($demande->date_fin)->addDay()->format('d/m/Y') }}</strong>.
        <br><br>
        En foi de quoi, le présent certificat est établi pour servir et valoir ce que de droit.
    </div>

    <!-- SIGNATURE -->
    <div class="signature">
        <strong>Le Directeur Général</strong><br>

           @if(!empty($b64Signature))
    <img src="{{ $b64Signature }}" style="width:140px;">
@else
    <p style="color:red;">Signature non disponible</p>
@endif


        <br>(Nom et prénom du responsable)
    </div>

    <!-- AMPLIATIONS -->
    <div class="footer">
        <strong>Ampliations :</strong><br>
        - Toutes les Directions de l’ANPTIC<br>
        - Chrono<br>
        - Intéressé<br>
        - DI
    </div>

</body>
</html>
