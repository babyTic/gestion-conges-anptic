<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ANPTIC</title>
    <style>

        :root {
            --primary: #2a2d78;
            --secondary: #656ed3;
            --accent: #44aae0;
            --text: #000000;
            --background: #ebefff;
            --white: #ffffff;
            --input-border: #656ed3;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --error: #e74c3c;
            --success: #2ecc71;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            min-height: 600px;
        }

        .image-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
        }

        .image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .image-placeholder i {
            font-size: 64px;
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .image-placeholder p {
            margin-top: 15px;
            font-size: 14px;
            max-width: 300px;
        }

        .form-section {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: var(--primary);
            font-size: 28px;
            margin-bottom: 5px;
        }

        .logo span {
            color: var(--secondary);
            font-size: 14px;
        }

        h2 {
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--input-border);
            border-radius: 50px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--secondary);
            font-size: 14px;
            padding: 5px;
        }

        button.login-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        button.login-btn:hover {
            background: #1a1c5a;
        }

        .auth-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .auth-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: var(--text);
            font-size: 14px;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .image-options {
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            font-size: 13px;
        }

        .image-options h3 {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .image-options ul {
            text-align: left;
            margin-left: 20px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            width: auto;
            margin-right: 8px;
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            display: block;
        }

        .error {
            background-color: #ffebee;
            color: var(--error);
            border: 1px solid #ffcdd2;
        }

        .success {
            background-color: #e8f5e9;
            color: var(--success);
            border: 1px solid #c8e6c9;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .image-section {
                padding: 30px;
            }

            .footer {
                position: relative;
            }
        }
       </style>
</head>
<body>
<div class="container">
    <div class="image-section">
        <div class="image-placeholder">
            <i>🔐</i>
            <h3>Espace de Connexion Sécurisé</h3>
            <p>Connectez-vous à votre compte ANPTIC pour accéder à toutes les fonctionnalités.</p>
            <div class="image-options">
                <img class="auth-image" src="{{ asset('assets/images/Other 4-log in.png') }}" alt="ANPTIC">
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="logo">
            <h1>ANPTIC</h1>
            <span>Plateforme de gestion</span>
        </div>

        <h2>Connexion</h2>

        <!-- Messages Laravel -->
        @if ($errors->any())
            <div class="message error">
                Identifiant ou mot de passe incorrect
            </div>
        @endif

        @if (session('success'))
            <div class="message success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Formulaire qui envoie vers AuthController@login -->
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="identifiant">Identifiant:</label>
                <input type="text" id="identifiant" name="identifiant" value="{{ old('identifiant') }}" required placeholder="Votre identifiant">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required placeholder="Votre mot de passe">
                    <button type="button" class="toggle-password" id="togglePassword">👁️</button>
                </div>
            </div>

            <div class="remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>
               <a href="{{ route('password.identifiant.submit') }}" class="forgot-password">Mot de passe oublié ?</a>

            </div>

            <button type="submit" class="login-btn">Se connecter</button>
        </form>

        <div class="auth-link">
            <p>Vous n'avez pas de compte? <a href="{{ route('register') }}">S'inscrire</a></p>
        </div>

        <div class="footer">
            © 2025 – ANPTIC – Tous droits réservés
        </div>
    </div>
</div>

<script>
    // Petit script juste pour afficher/masquer le mot de passe
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            this.textContent = '🔒';
        } else {
            passwordInput.type = 'password';
            this.textContent = '👁️';
        }
    });
</script>
</body>
</html>
