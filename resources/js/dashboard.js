console.log("✅ dashboard.js chargé !");
document.addEventListener("DOMContentLoaded", () => {
    console.log("✅ dashboard.js chargé !");
    
    const userRole = window.userRole || null;

    if (userRole) {
        console.log(`👤 Rôle détecté : ${userRole}`);
        restreindreElements(userRole);
    }
});

function restreindreElements(role) {
    // Exemple : masquer certains boutons ou sections selon le rôle
    const adminOnly = document.querySelectorAll(".only-admin");
    const rhOnly = document.querySelectorAll(".only-rh");
    const respOnly = document.querySelectorAll(".only-resp");

    if (role !== "admin") adminOnly.forEach(el => el.style.display = "none");
    if (role !== "rh") rhOnly.forEach(el => el.style.display = "none");
    if (role !== "responsable") respOnly.forEach(el => el.style.display = "none");
}


document.addEventListener('DOMContentLoaded', function () {
    console.groupCollapsed("🟢 Initialisation Dashboard JS");

    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const menuToggle = document.getElementById('menuToggle');
    const logoContainer = document.querySelector('.logo-container');
    const notificationContainer = document.getElementById('notification-container');
    const notificationBadge = document.getElementById('notification-badge'); // Ajoute un badge dans ton header
    let notificationCount = 0;

    // === Toggle sidebar collapse (desktop) ===
    function toggleSidebar() {
        if (!sidebar || !mainContent || !sidebarToggle) {
            console.warn("⚠️ Sidebar ou mainContent introuvable, impossible de toggler.");
            return;
        }
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');

        const icon = sidebarToggle.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-chevron-left', !sidebar.classList.contains('collapsed'));
            icon.classList.toggle('fa-chevron-right', sidebar.classList.contains('collapsed'));
        }
    }

    if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
    if (logoContainer) {
        logoContainer.addEventListener('click', toggleSidebar);
        logoContainer.style.cursor = "pointer";
    }

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    }

    // === Close sidebar on mobile click outside ===
    document.addEventListener('click', e => {
        if (sidebar && window.innerWidth <= 1024 && !sidebar.contains(e.target) &&
            menuToggle && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });

    // === Adjust on resize ===
    if (sidebar) window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) sidebar.classList.remove('open');
    });

    // === Toggle submenus ===
    document.querySelectorAll('.nav-link.has-submenu').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const parent = link.closest('.nav-item');
            const subMenu = parent?.querySelector('.sub-menu');
            const icon = link.querySelector('.toggle-icon');

            document.querySelectorAll('.sub-menu').forEach(menu => {
                if (menu !== subMenu) menu.classList.remove('open');
            });
            document.querySelectorAll('.toggle-icon').forEach(ic => {
                if (ic !== icon) ic.classList.remove('rotated');
            });

            if (subMenu) subMenu.classList.toggle('open');
            if (icon) icon.classList.toggle('rotated');
        });
    });

    // === Notification System with Badge ===
    function updateNotificationBadge() {
        if (!notificationBadge) return;
        notificationBadge.textContent = notificationCount > 0 ? notificationCount : '';
        notificationBadge.style.display = notificationCount > 0 ? 'inline-block' : 'none';
    }

    function showNotification(user, message, avatar) {
        if (!notificationContainer) {
            console.warn("⚠️ Notification container introuvable.");
            return;
        }

        notificationCount++;
        updateNotificationBadge();

        const notif = document.createElement('div');
        notif.classList.add('notification');
        notif.innerHTML = `
            <div class="avatar">
                <img src="${avatar}" alt="avatar" onerror="this.src='/assets/images/default-avatar.png'">
            </div>
            <div class="content">
                <div class="title">${user}</div>
                <div class="message">${message}</div>
            </div>
            <button class="mark-read">Lu</button>
        `;

        notif.querySelector('.mark-read').addEventListener('click', () => {
            notif.style.animation = "slideOut 0.5s forwards";
            setTimeout(() => {
                notif.remove();
                notificationCount--;
                updateNotificationBadge();
            }, 500);
        });

        setTimeout(() => {
            notif.style.animation = "slideOut 0.5s forwards";
            setTimeout(() => {
                notif.remove();
                notificationCount--;
                updateNotificationBadge();
            }, 500);
        }, 5000);

        notificationContainer.appendChild(notif);
    }


      // === FullCalendar ===
    const calendarEl = document.getElementById('calendar');
    if (calendarEl && typeof FullCalendar !== 'undefined') {

        const colors = {
            approuve_rh: '#16a34a',          // vert
            approuve_responsable: '#2563eb', // bleu
            approuve_dg: '#10b981',          // vert émeraude
            termine: '#9333ea',              // violet
            soumis: '#f59e0b',           // orange
            rejete: '#ef4444',               // rouge vif
            en_cours: '#3b82f6',             // bleu clair
            autre: '#6b7280'                 // gris neutre
        };

        // Crée le calendrier
        var allEvents = window.calendarEvents ?? [];

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: allEvents,

            eventDidMount: function (info) {
                const statut = info.event.extendedProps.statut?.toLowerCase() || 'autre';
                const bgColor = colors[statut] || colors.autre;
                const textColor = '#fff';

                // Application de la couleur
                info.el.style.setProperty('background-color', bgColor, 'important');
                info.el.style.setProperty('border-color', bgColor, 'important');
                info.el.style.setProperty('color', textColor, 'important');

                const main = info.el.querySelector('.fc-event-main');
                if (main) {
                    main.style.setProperty('background-color', bgColor, 'important');
                    main.style.setProperty('color', textColor, 'important');
                }

                // Tooltip (tippy)
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

        console.log("📌 Événements envoyés au calendrier :", allEvents);
        calendar.render();

        // === Légende cliquable ===
        const legendContainer = document.getElementById('calendar-legend');
        if (legendContainer) {
            const legendItems = legendContainer.querySelectorAll('.legend-item');

            legendItems.forEach(item => {
                item.addEventListener('click', () => {
                    const selected = item.dataset.status;

                    // Gestion du style actif
                    legendItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');

                    // Filtrage
                    const filtered = allEvents.filter(ev => ev.statut === selected);
                    calendar.removeAllEvents();
                    calendar.addEventSource(filtered);

                    console.log(`🎯 Filtre appliqué : ${selected} (${filtered.length} événements)`);
                });
            });

            // Double clic pour réinitialiser
            legendContainer.addEventListener('dblclick', () => {
                legendItems.forEach(i => i.classList.remove('active'));
                calendar.removeAllEvents();
                calendar.addEventSource(allEvents);
                console.log("♻️ Filtre réinitialisé (tous les statuts affichés)");
            });
        }

    } else {
        console.warn("ℹ️ Pas de calendrier ou FullCalendar non chargé.");
    }

    console.groupEnd();


});



// === Search User ===
const searchInput = document.getElementById('searchUser');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        let value = this.value.toLowerCase();
        document.querySelectorAll('#usersTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });
}

// === Inline Edit ===
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-edit-inline').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const editForm = row?.querySelector('.edit-inline-form');
            if (editForm) editForm.classList.toggle('d-none');
        });
    });
});


