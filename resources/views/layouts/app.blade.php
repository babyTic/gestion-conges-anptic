<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANPTIC - @yield('title', 'Gestion des Congés')</title>

    <!-- CSS Global -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Spécifiques aux pages -->
    <link rel="stylesheet" href="{{ asset('css/auth/auth-common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">


    @yield('styles')
</head>
<body>
@yield('content')

<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>
