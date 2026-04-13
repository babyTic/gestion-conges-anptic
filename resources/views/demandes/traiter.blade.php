@extends('layouts.dashboard')

@section('title', 'Traitement des demandes')
@section('page-title', ' Traitement des demandes')

@section('content')

@php
    $statusLabels = [
        'soumis' => 'Soumis',
        'approuve_responsable' => 'Approuvé (Responsable)',
        'approuve_rh' => 'Approuvé (RH)',
        'en_attente_signature_dg' => 'En attente signature DG',
        'approuve_dg' => 'Approuvé (DG)',
        'rejete' => 'Rejeté',
        'termine' => 'Terminé',
    ];
    $role = strtolower(auth()->user()->role ?? '');
@endphp

<div class="requests-card">
    <h2 class="chart-title">📋 Liste des demandes à traiter</h2>

    <!-- FILTRES -->
    <form method="GET" action="{{ route('demandes.traiter') }}" class="mb-4" style="display:flex; gap:15px; margin-bottom:20px;">
        <select name="direction" class="form-control" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
            <option value="">-- Toutes les directions --</option>
            @foreach($directions as $direction)
                <option value="{{ $direction->id }}" {{ request('direction') == $direction->id ? 'selected' : '' }}>
                    {{ $direction->nom }}
                </option>
            @endforeach
        </select>

        <select name="statut" class="form-control" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
            <option value="">-- Tous les statuts --</option>
            @foreach($statusLabels as $key => $label)
                <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <input type="text" name="nom" placeholder="Nom / Prénom" class="form-control"
               value="{{ request('nom') }}" style="padding:8px;border:1px solid #ddd;border-radius:6px;">

        <button type="submit" class="btn btn-primary">Filtrer</button>
    </form>

    <!-- TABLE -->
    <div class="table-responsive">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:2px solid #eee;">
                    <th style="padding:8px;">Employé</th>
                    <th style="padding:8px;">Type</th>
                    <th style="padding:8px;">Date début</th>
                    <th style="padding:8px;">Date fin</th>
                    <th style="padding:8px;">Lieu</th>
                    <th style="padding:8px;">Direction</th>
                    <th style="padding:8px;">Solde restant</th>
                    <th style="padding:8px;">Statut</th>
                    <th style="padding:8px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($demandes as $demande)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:8px;">{{ $demande->user->prenom }} {{ $demande->user->nom }}</td>
                        <td style="padding:8px;">{{ $demande->type->nom ?? 'N/A' }}</td>
                        <td style="padding:8px;">{{ $demande->date_debut }}</td>
                        <td style="padding:8px;">{{ $demande->date_fin }}</td>
                        <td style="padding:8px;">{{ $demande->lieu ?? '-' }}</td>
                        <td>{{ $demande->user->direction->nom ?? '—' }}</td>
                        <td>
   <p> {{ $demande->solde_restant ?? 'N/A' }} jours</p>

                        </td>

                        <td style="padding:8px;">
                            @php $lbl = $statusLabels[$demande->statut] ?? ucfirst(str_replace('_',' ', $demande->statut)); @endphp
                            <span class="status-badge
                                {{ in_array($demande->statut, ['approuve_dg','approuve_rh','approuve_responsable']) ? 'status-approved' :
                                   (in_array($demande->statut, ['rejete']) ? 'status-rejected' : 'status-pending') }}">
                                {{ $lbl }}
                            </span>
                        </td>

                        <td style="padding:8px; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                          
 @if($demande->piece_jointe)
                        <a href="{{ asset('storage/' . $demande->piece_jointe) }}

" target="_blank" class="btn btn-sm btn-info">
                        📎 Voir la pièce jointe
                        </a>
                        @else
                        <em>Aucune</em>
                            @endif

                            {{-- Voir PDF de la demande --}}
                            @if(!empty($demande->document) && \Storage::disk('public')->exists($demande->document))
                                <a href="{{ asset('storage/'.$demande->document) }}" target="_blank" class="btn btn-sm btn-secondary">📄 PDF</a>
                            @endif

                            {{-- Responsable: Accepter / Refuser (si soumis) --}}
                            @if($role === 'responsable' && $demande->statut === 'soumis')
                                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="approuve_responsable">
                                    <button type="submit" class="btn btn-sm btn-success">✔ Accepter</button>
                                </form>

                                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="rejete">
                                    <button type="submit" class="btn btn-sm btn-danger">✖ Refuser</button>
                                </form>
                            @endif

                            {{-- RH: peut traiter les demandes 'soumis' et 'approuve_responsable' --}}
                            @if($role === 'rh' && in_array($demande->statut, ['soumis','approuve_responsable']))
                                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="approuve_rh">
                                    <button type="submit" class="btn btn-sm btn-success">✔ Approuver RH</button>
                                </form>

                                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="rejete">
                                    <button type="submit" class="btn btn-sm btn-danger">✖ Refuser</button>
                                </form>
                            @endif

                            {{-- RH: Générer autorisation (après avoir approuvé RH) --}}
                            @if($role === 'rh' && $demande->statut === 'approuve_rh')
                                <a href="{{ route('demandes.autorisation.generer', $demande->id) }}" class="btn btn-sm btn-primary">
                                    📝 Générer autorisation
                                </a>
                            @endif

                            {{-- DG: Ouvrir modal signature si en attente signature DG --}}
@if($role === 'dg' && $demande->statut === 'en_attente_signature_dg')
    <!-- bouton ouvre modale (ID unique avec underscore) -->
    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#signModal_{{ $demande->id }}">
        ✍️ Signer
    </button>

    <!-- Modal signature (ID unique avec underscore) -->
    <div class="modal fade" id="signModal_{{ $demande->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Signer autorisation — {{ $demande->user->prenom }} {{ $demande->user->nom }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Signer dans la zone ci-dessous (souris / tactile) puis valider :</p>
            <canvas id="sigCanvas_{{ $demande->id }}" width="560" height="150" style="border:1px solid #ddd; width:100%;"></canvas>
            <div style="margin-top:8px; display:flex; gap:8px;">
                <button id="clearSig_{{ $demande->id }}" type="button" class="btn btn-outline-secondary">Effacer</button>

                <form id="signForm_{{ $demande->id }}" method="POST" action="{{ route('demandes.autorisation.signer', $demande->id) }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="signature_data" id="signature_data_{{ $demande->id }}">
                    <button type="submit" class="btn btn-primary">Enregistrer signature</button>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
@endif
  {{-- DG: Signature du certificat de reprise --}}
@if($role === 'dg' && $demande->statut === 'en_attente_signature_dg')
    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#signModalCert_{{ $demande->id }}">
        ✍️ Signer le certificat
    </button>

    <!-- Modal signature certificat -->
    <div class="modal fade" id="signModalCert_{{ $demande->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Signer certificat de reprise — {{ $demande->user->prenom }} {{ $demande->user->nom }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Veuillez signer ci-dessous (souris ou tactile) :</p>
                    <canvas id="sigCanvasCert_{{ $demande->id }}" width="560" height="150" style="border:1px solid #ddd; width:100%;"></canvas>
                    <div class="mt-2 d-flex gap-2">
                        <button id="clearSigCert_{{ $demande->id }}" type="button" class="btn btn-outline-secondary">
                            Effacer
                        </button>
                        <form id="signFormCert_{{ $demande->id }}" 
                              method="POST" 
                              action="{{ route('demandes.certificat.signer', $demande->id) }}">
                            @csrf
                            <input type="hidden" name="signature_data" id="signature_data_cert_{{ $demande->id }}">
                            <button type="submit" class="btn btn-primary">
                                Enregistrer signature
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

     {{-- Télécharger autorisation signée (si disponible) --}}
                            @if(!empty($demande->autorisation_signee_path) && \Storage::disk('public')->exists($demande->autorisation_signee_path))
                                <a href="{{ route('demandes.autorisation.telecharger', $demande->id) }}" class="btn btn-sm btn-info">
                                    ⬇ Autorisation signée
                                </a>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding:12px;">Aucune demande trouvée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="mt-4">
        {{ $demandes->appends(request()->query())->links() }}
    </div>
</div>

<!-- JS: signature canvas (générique pour plusieurs modales) -->
<script>
    // === Gestion signature certificat DG ===
document.querySelectorAll('[id^="sigCanvasCert_"]').forEach(function(canvas) {
    const id = canvas.id.replace('sigCanvasCert_', '');
    const c = canvas;
    const ctx = c.getContext('2d');
    ctx.strokeStyle = "#111";
    ctx.lineWidth = 2;
    let drawing = false;
    let lastX = 0, lastY = 0;

    function pointerStart(e){
        drawing = true;
        const rect = c.getBoundingClientRect();
        lastX = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
        lastY = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
    }
    function pointerMove(e){
        if(!drawing) return;
        if (e.cancelable) e.preventDefault();
        const rect = c.getBoundingClientRect();
        const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
        const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(x, y);
        ctx.stroke();
        lastX = x; lastY = y;
    }
    function pointerEnd(){ drawing = false; }

    c.addEventListener('mousedown', pointerStart);
    c.addEventListener('mousemove', pointerMove);
    c.addEventListener('mouseup', pointerEnd);
    c.addEventListener('mouseout', pointerEnd);
    c.addEventListener('touchstart', pointerStart, {passive: true});
    c.addEventListener('touchmove', pointerMove, {passive: false});
    c.addEventListener('touchend', pointerEnd, {passive: true});

    const clearBtn = document.getElementById('clearSigCert_' + id);
    if (clearBtn) {
        clearBtn.addEventListener('click', function(){
            ctx.clearRect(0, 0, c.width, c.height);
        });
    }

    const signForm = document.getElementById('signFormCert_' + id);
    if (signForm) {
        signForm.addEventListener('submit', function(e) {
            const dataUrl = c.toDataURL('image/png');
            const input = document.getElementById('signature_data_cert_' + id);
            if (input) input.value = dataUrl;
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // initialiser pour chaque canvas présent sur la page (prefix sigCanvas_)
    document.querySelectorAll('[id^="sigCanvas_"]').forEach(function(canvas) {
        const id = canvas.id.replace('sigCanvas_','');
        const c = canvas;
        const ctx = c.getContext('2d');
        ctx.strokeStyle = "#111";
        ctx.lineWidth = 2;
        let drawing = false;
        let lastX = 0, lastY = 0;

        function pointerStart(e){
            drawing = true;
            const rect = c.getBoundingClientRect();
            lastX = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
            lastY = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
        }
        function pointerMove(e){
            if(!drawing) return;
            if (e.cancelable) e.preventDefault(); // important pour dessiner sur mobile
            const rect = c.getBoundingClientRect();
            const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
            const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.stroke();
            lastX = x; lastY = y;
        }
        function pointerEnd(){
            drawing = false;
        }

        // souris
        c.addEventListener('mousedown', pointerStart);
        c.addEventListener('mousemove', pointerMove);
        c.addEventListener('mouseup', pointerEnd);
        c.addEventListener('mouseout', pointerEnd);

        // tactile (passive options)
        c.addEventListener('touchstart', pointerStart, {passive: true});
        c.addEventListener('touchmove', pointerMove, {passive: false});
        c.addEventListener('touchend', pointerEnd, {passive: true});

        // clear button (id unique)
        const clearBtn = document.getElementById('clearSig_' + id);
        if (clearBtn) {
            clearBtn.addEventListener('click', function(){
                ctx.clearRect(0,0,c.width,c.height);
            });
        }

        // on submit of the corresponding form, set hidden input with base64 data
        const signForm = document.getElementById('signForm_' + id);
        if (signForm) {
            signForm.addEventListener('submit', function(e) {
                const dataUrl = c.toDataURL('image/png');
                const input = document.getElementById('signature_data_' + id);
                if (input) {
                    input.value = dataUrl;
                }
                // allow submit to continue
            });
        }
    });
});
</script>


@endsection
