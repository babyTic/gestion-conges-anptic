<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ANPTIC - Authentification')</title>

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS compilé -->
    @vite(['resources/css/auth.css'])
</head>
<body class="auth-body">

    <div class="auth-container">
        <!-- Logo -->
        <div class="auth-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo ANPTIC">
        </div>

        <!-- Zone dynamique -->
        <div class="auth-content">
            @yield('content')
        </div>
    </div>

    <!-- JS -->
    @vite(['resources/js/auth.js'])
</body>
</html>
