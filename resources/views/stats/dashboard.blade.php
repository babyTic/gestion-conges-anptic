@extends('layouts.dashboard')

@section('title', 'Statistiques')
@section('page-title', '📊 Tableau de bord')

@section('content')
<div class="stats-container">
    <!-- KPI cards -->
    <div class="stats-card">
        <h3>📑 Demandes</h3>
        <p class="text-blue-600">{{ $totalDemandes ?? 0 }}</p>
        <div class="flex items-center text-xs text-green-500">
            <img src="{{ asset('assets/images/conforme.png') }}" alt="ANPTIC logo" class="logo">
            <img src="{{ asset('assets/images/document.gif') }}"  class="stats-gif">
        </div>
    </div>


    <div class="stats-card">
        <h3>👥 Utilisateurs</h3>
        <p class="text-green-600">{{ $totalAgents ?? 0 }}</p>
        <div class="flex items-center text-xs text-green-500">
            <img src="{{ asset('assets/images/groupe.png') }}" alt="ANPTIC logo" class="logo">
            <img src="{{ asset('assets/images/worker.gif') }}"  class="stats-gif">
                    </div>
    </div>

    <div class="stats-card">
        <h3>🛌 Utilisateurs en congé</h3>
        <p class="text-yellow-600">{{ $agentsEnConge ?? 0 }}</p>
        <div class="flex items-center text-xs text-green-500">
            <img src="{{ asset('assets/images/jour-de-conge.png') }}" alt="ANPTIC logo" class="logo">
          <img src="{{ asset('assets/images/travel.gif') }}"  class="stats-gif">
        </div>
    </div>
</div>

<!-- Bloc graphiques -->
<div class="charts-grid mt-6">
    <!-- Courbe -->
   <div class="chart-card chart-large" style="width:150%; max-width: 1100px; flex: 1 1 100%;">
    <h2>📈 Nombre d'utilisateurs</h2>
    <div class="chart-wrapper" style="min-height: 400px;">
        <canvas id="usersLineChart"></canvas>
    </div>
</div>


    <!-- Top onglets 
    <div class="chart-card chart-side">
        <h2>📊 Top onglet utilisé</h2>
        <ul>
            <li>📊 Statistique — 35%</li>
            <li>📅 Calendrier — 22%</li>
            <li>🔔 Notification — 15%</li>
            <li>➕ Nouvelle demande — 10%</li>
            <li>⚙️ Paramètre — 9%</li>
            <li>🗂️ Journaux — 9%</li>
        </ul>
    </div> -->
</div>

<!-- Répartition -->
<div class="charts-grid mt-6">
    <!-- Bar chart -->
    <div class="chart-card">
        <h2>👔 Répartition par rôle</h2>
        <div class="chart-wrapper">
            <canvas id="rolesChart"></canvas>
        </div>
    </div>

    <!-- Donut -->
    <div class="chart-card">
        <h2>🏢 Répartition par département</h2>
        <div class="chart-wrapper">
            <canvas id="departmentsChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {

    // ==========================
    // Données dynamiques (PHP)
    // ==========================
    const monthlyLabels = @json($monthlyLabels);
    const monthlyDataThisYear = @json($monthlyDataThisYear);
    const monthlyDataLastYear = @json($monthlyDataLastYear);
    const roles = @json($rolesLabels);
    const rolesCount = @json($rolesData);
    const departments = @json($departmentsLabels);
    const departmentsData = @json($departmentsData);

    // ==========================
    // Styles globaux Chart.js
    // ==========================
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = "#334155";

    // Fonction utilitaire couleur dégradée
    const createGradient = (ctx, color1, color2) => {
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    };

    // ==========================
    // 1️⃣ Courbe : évolution des demandes
    // ==========================
    const ctxUsers = document.getElementById('usersLineChart').getContext('2d');
    new Chart(ctxUsers, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [
                {
                    label: "Cette année",
                    data: monthlyDataThisYear,
                    borderColor: "#3b82f6",
                    backgroundColor: createGradient(ctxUsers, "rgba(59,130,246,0.3)", "rgba(59,130,246,0)"),
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: "Année passée",
                    data: monthlyDataLastYear,
                    borderColor: "#94a3b8",
                    borderDash: [6,6],
                    fill: false,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1'
                }
            },
            scales: {
                y: { grid: { color: 'rgba(203,213,225,0.3)' }, beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });

    // ==========================
    // 2️⃣ Bar chart : répartition par rôle
    // ==========================
    const ctxRoles = document.getElementById('rolesChart').getContext('2d');
    new Chart(ctxRoles, {
        type: 'bar',
        data: {
            labels: roles,
            datasets: [{
                label: "Nombre d'utilisateurs",
                data: rolesCount,
                backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => `${ctx.raw} utilisateurs` } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(203,213,225,0.3)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ==========================
    // 3️⃣ Donut : répartition par département
    // ==========================
    const ctxDept = document.getElementById('departmentsChart').getContext('2d');
    new Chart(ctxDept, {
        type: 'doughnut',
        data: {
            labels: departments,
            datasets: [{
                data: departmentsData,
                backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#6366f1']
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                            const val = ctx.raw;
                            const perc = ((val / total) * 100).toFixed(1);
                            return `${ctx.label}: ${val} (${perc}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    // ==========================
    // 4️⃣ Dynamisation "Top onglet utilisé"
    // ==========================
    const topTabs = [
        { name: "📊 Statistiques", value: 35 },
        { name: "📅 Calendrier", value: 22 },
        { name: "🔔 Notifications", value: 15 },
        { name: "➕ Nouvelle demande", value: 10 },
        { name: "⚙️ Paramètres", value: 9 },
        { name: "🗂️ Journaux", value: 9 },
    ];

    const list = document.querySelector('.chart-side ul');
    list.innerHTML = "";
    topTabs.forEach(tab => {
        const li = document.createElement("li");
        li.classList.add("hover:bg-slate-100","rounded-md","transition","p-1");
        li.innerHTML = `
            <div class="flex justify-between items-center">
                <span>${tab.name}</span>
                <span class="text-slate-500 text-sm">${tab.value}%</span>
            </div>
            <div class="h-2 bg-slate-200 rounded-md overflow-hidden mt-1">
                <div class="h-full bg-blue-500 rounded-md transition-all duration-700" style="width:${tab.value}%;"></div>
            </div>
        `;
        list.appendChild(li);
    });

});
</script>

@endsection
