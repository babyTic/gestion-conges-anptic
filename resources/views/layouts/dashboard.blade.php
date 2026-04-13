<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - ANPTIC')</title>

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

{{-- FullCalendar CSS (mettre avant le JS) --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
   <!-- Bootstrap JS (bundle = Bootstrap + Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>




    <!-- CSS compilé -->
    @vite(['resources/css/dashboard.css'])

</head>
<body>
<!-- Sidebar -->
@include('partials.sidebar')

<!-- Contenu principal -->
<div class="main-content" id="mainContent">
    <div class="header">
        <div style="display:flex; align-items:center; gap:10px;">

        <!-- ✅ Bouton retour -->
   <form action="{{ url()->previous() }}" method="GET" style="display:inline;">
    <button type="submit" class="btn-menu-like btn-secondary-like">
         Revenir
    </button>
</form> 

        
        <!-- Titre dynamique -->
        <h1 class="page-title">@yield('page-title', 'Tableau de bord')</h1>
    </div>
  <!--  <button id="toggleDark" class="btn btn-secondary">🌙 Mode sombre</button> -->

    <!-- Déconnexion -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </button>
    </form> 
</div>



    {{-- Zone dynamique --}}
    @yield('content')

    <div class="footer">
        © {{ date('Y') }} – ANPTIC – Tous droits réservés
    </div>
</div>
<<!-- 
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleDark = document.getElementById('toggleDark');

    if (!toggleDark) {
        console.warn("⚠️ Bouton #toggleDark introuvable !");
        return;
    }

    const applyTheme = (theme) => {
        document.body.classList.toggle('dark-mode', theme === 'dark');
        toggleDark.textContent = theme === 'dark' ? '☀️ Mode clair' : '🌙 Mode sombre';
    };

    // Charger le thème stocké
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    // Toggle au clic
    toggleDark.addEventListener('click', () => {
        const newTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme);
    });
}); </script>-->
<script>
window.calendarEvents = @json($events ?? []); 
window.userRole = "{{ Auth::user()->role }}";
</script> 


<!-- JS -->
@vite(['resources/js/dashboard.js'])

{{-- Scripts injectés dynamiquement depuis les vues --}}
@stack('scripts')
</body>
</html>

</body>
</html>
