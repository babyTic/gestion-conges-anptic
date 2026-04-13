<!-- Sidebar -->
@php $role = strtolower(Auth::user()->role ?? '') @endphp


<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <img src="{{ asset('assets/images/anptic.png') }}" alt="ANPTIC logo" class="logo">
            <span class="logo-text">ANPTC</span>
        </div>
        <div class="toggle-btn" id="sidebarToggle">
            <i class="fas fa-chevron-left"></i>
        </div>
    </div>

    <div class="nav-menu">
        <!-- Dashboard -->
        @if(in_array($role, ['agent','rh','responsable','dg','admin']))
        <div class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </div>
        @endif

        <!-- Demande -->

        <div class="nav-item">
            <a href="#" class="nav-link has-submenu">
                <i class="fas fa-inbox"></i>
                <span class="nav-text">Demande</span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </a>
            <div class="sub-menu {{ request()->routeIs('demandes.*') ? 'open' : '' }}">
                <a href="{{ route('demandes.create') }}" class="nav-link">Nouvelle demande</a>
                <a href="{{ route('demandes.index') }}" class="nav-link">Mes demandes</a>
                 @if(in_array($role, ['rh','responsable','dg']))              
                  <a href="{{ route('demandes.traiter') }}" class="nav-link">Traiter</a>
                  <a href="{{ route('decision.edit') }}" class="nav-link">
                <span class="nav-text">RH</span>
            </a>
                @endif
                <a href="{{ route('demandes.historique') }}" class="nav-link">Historique</a>
            </div>
        </div>

        <!-- Congés -->
        <div class="nav-item">
            <a href="#" class="nav-link has-submenu">
                <i class="fas fa-umbrella-beach"></i>
                <span class="nav-text">Congés</span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </a>
            <div class="sub-menu {{ request()->routeIs('conges.*') ? 'open' : '' }}">
                <a href="{{ route('conges.solde') }}" class="nav-link">Solde</a>
                <a href="{{ route('conges.planification') }}" class="nav-link">Planification</a>
                
            </div>
        </div>

        <!-- Statistiques -->
        <div class="nav-item">
            <a href="#" class="nav-link has-submenu">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-text">Statistiques</span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </a>
            <div class="sub-menu {{ request()->routeIs('stats.*') ? 'open' : '' }}">
                <a href="{{ route('stats.dashboard') }}" class="nav-link">Tableaux de bord</a>
                @if($role === 'rh')
                <a href="{{ route('stats.reports') }}" class="nav-link">Rapports</a>
                @endif
            </div>
        </div>

        <!-- Notifications 
        <div class="nav-item">
            <a href="{{ route('notifications.index') }}" class="nav-link">
                <i class="fas fa-bell"></i>
                <span class="nav-text">Notifications</span>
            </a>
        </div> -->

        <!-- Paramètres -->
     
        <div class="nav-item">
            @if($role === 'admin')
  <a href="{{ route('settings.index') }}" class="nav-link">
                 <i class="fas fa-cog"></i>
                <span class="nav-text">Paramètres</span>
            </a>
             @endif
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->prenom, 0, 1) . substr(Auth::user()->nom, 0, 1)) }}
        </div>
        <div class="user-info">
            <div class="user-welcome">Bon retour 👋 {{ ucfirst(Auth::user()->role) }}</div>
            <div class="user-name">{{ Auth::user()->prenom }} {{ strtoupper(Auth::user()->nom) }}</div>
        </div>
        <i class="fas fa-caret-down"></i>
    </div>
</div>


