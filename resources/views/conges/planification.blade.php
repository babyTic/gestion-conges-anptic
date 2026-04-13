@extends('layouts.dashboard')

@section('title', 'Planification des congés')
@section('page-title', '📅 Planification des congés')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-md">
    <h2 class="text-xl font-bold mb-4">📆 Vue globale des congés approuvés</h2>
    <form method="GET" class="mb-4 flex gap-3">
        <select name="direction" class="form-control w-64" onchange="this.form.submit()">
            <option value="">Toutes les directions</option>
            @foreach($directions as $dir)
                <option value="{{ $dir->id }}" {{ request('direction') == $dir->id ? 'selected' : '' }}>
                    {{ $dir->nom }}
                </option>
            @endforeach
        </select>
    </form>
    <div id="calendar-legend" class="legend">
    <span class="legend-item" data-status="approuve_rh">🟢 Approuvé RH</span>
    <span class="legend-item" data-status="approuve_responsable">🔵 Approuvé Responsable</span>
    <span class="legend-item" data-status="approuve_dg">🔵 Approuvé DG</span>
    <span class="legend-item" data-status="termine">🟣 Terminé</span>
    <span class="legend-item" data-status="soumis">🟠 En attente</span>
    <span class="legend-item" data-status="rejete">🔴 Rejeté</span>
</div>

<div id="calendar"></div>


 
    <div id="calendar-empty" style="display:none;margin-top:16px;color:#6b7280;">
        Aucun congé approuvé à afficher.
    </div>

</div>
@endsection


{{-- FullCalendar + Popper + Tippy --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // events fournis depuis le controller via blade
    const events = window.calendarEvents ?? @json($events ?? []);

    console.log("planification: ✅ FullCalendar initialisé");

    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        firstDay: 1,
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: events, // <-- utilise le tableau local, PAS d'URL
eventDidMount: function (info) {
    // Récupération du statut (ex: approuve_dg, termine, en_attente, etc.)
    const statut = info.event.extendedProps.statut?.toLowerCase() || 'autre';

    // 🎨 Palette personnalisée par statut :
    const couleurs = {
        approuve_dg:  '#10b981', // vert émeraude
        termine:      '#15803d', // vert foncé
        en_attente:   '#f59e0b', // orange
        rejete:       '#ef4444', // rouge vif
        en_cours:     '#3b82f6', // bleu
        autre:        '#6b7280'  // gris neutre
    };

    const bgColor = couleurs[statut] || couleurs.autre;
    const textColor = '#fff';

    // Application sur les éléments du DOM
    info.el.style.setProperty('background-color', bgColor, 'important');
    info.el.style.setProperty('border-color', bgColor, 'important');
    info.el.style.setProperty('color', textColor, 'important');

    const main = info.el.querySelector('.fc-event-main');
    if (main) {
        main.style.setProperty('background-color', bgColor, 'important');
        main.style.setProperty('color', textColor, 'important');
    }

    console.log(`🎨 [Fix couleur] ${info.event.title} (${statut}) → ${bgColor}`);

    // === Tooltip Tippy ===
    if (typeof tippy !== 'undefined') {
        tippy(info.el, {
            content: `
                <strong>${info.event.title}</strong><br>
                📅 ${info.event.startStr} → ${info.event.endStr}<br>
                🏢 ${info.event.extendedProps.direction ?? 'N/A'}<br>
                📍 ${info.event.extendedProps.lieu ?? '-'}<br>
                🔖 Statut : <strong>${statut}</strong>
            `,
            allowHTML: true,
            placement: 'top',
        });
    }
}



    });

    calendar.render();
});
</script>


