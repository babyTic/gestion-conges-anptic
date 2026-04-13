<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Succès - ANPTIC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .success-box {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.2);
        }
        .success-box i {
            font-size: 70px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        .success-box h2 {
            color: #2a2d78;
            margin-bottom: 15px;
        }
        .generated-id {
            font-size: 22px;
            font-weight: bold;
            color: #2a2d78;
            background: #f0f3ff;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px dashed #656ed3;
            letter-spacing: 2px;
        }
        .warning {
            color: #e74c3c;
            margin: 15px 0;
            font-weight: 500;
        }
        .btn {
            background-color: #3498db;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn:hover { background-color: #2980b9; }
        .copy-btn { background-color: #656ed3; margin-top: 10px; }
        .copy-btn:hover { background-color: #505ac9; }
    </style>
</head>
<body>
<div class="success-box">
    <i class="fas fa-check-circle"></i>
    <h2>Utilisateur ajouté 🎉</h2>
    <p>Félicitations <strong>{{ session('user_prenom') }} {{ session('user_nom') }}</strong> !</p>
    <a href="{{ route('settings.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Retour</a>
</div>


</body>
</html>
