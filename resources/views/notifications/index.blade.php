@extends('layouts.dashboard')

@section('title', 'Notifications')
@section('page-title', '🔔 Notifications')

@section('content')
<div id="notificationPanel" class="notification-panel open"><!-- 👈 déjà ouvert -->
    <div class="panel-header">
        <h3>🔔 Notifications</h3>
        <a href="{{ url()->previous() }}" class="btn-close-panel">✖</a>
    </div>
    <div class="panel-body" id="notificationList">
        <p class="loading-text">Chargement...</p>
    </div>
</div>

<!-- JS pour charger dynamiquement les notifications -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('notificationList');
    list.innerHTML = '<p class="loading-text">Chargement...</p>';

    fetch('{{ route("notifications.data") }}') // 👉 nouvelle route JSON
        .then(response => response.json())
        .then(data => {
            list.innerHTML = '';
            if (data.length === 0) {
                list.innerHTML = '<p class="empty-text">Aucune notification.</p>';
            } else {
                data.forEach(n => {
                    let status = n.status === 'approuve_dg' ? '✅ Approuvée'
                              : n.status === 'rejete' ? '❌ Rejetée'
                              : '⏳ En attente';

                    list.innerHTML += `
                        <div class="notif-item ${n.read_at ? 'read' : 'unread'}">
                            <p>${status} – ${n.message ?? 'Nouvelle demande'}</p>
                            <small>📅 ${new Date(n.created_at).toLocaleString()}</small>
                        </div>
                    `;
                });
            }
        })
        .catch(() => {
            list.innerHTML = '<p class="error-text">Erreur de chargement</p>';
        });
});
</script>

<!-- CSS -->
<style>
.notification-panel {
    position: fixed;
    top: 0;
    right: 0; /* 👈 directement visible */
    width: 350px;
    height: 100%;
    background: white;
    box-shadow: -2px 0 6px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    z-index: 9999;
    border-left: 1px solid #ddd;
    border-radius: 12px 0 0 12px;
}
.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f5f5f5;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
}
.panel-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
}
.notif-item {
    padding: 8px;
    border-bottom: 1px solid #eee;
    border-radius: 6px;
    margin-bottom: 6px;
}
.notif-item.unread {
    background: #eef6ff;
    font-weight: bold;
}
.notif-item.read {
    background: #fafafa;
    color: #666;
}
.btn-close-panel {
    border: none;
    background: none;
    font-size: 16px;
    cursor: pointer;
}
.loading-text, .empty-text, .error-text {
    text-align: center;
    color: #888;
    font-style: italic;
}
</style>
@endsection
