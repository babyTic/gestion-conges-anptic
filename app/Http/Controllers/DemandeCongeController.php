<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DemandeConge;
use App\Models\TypeConge;
use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Parametre;


class DemandeCongeController extends Controller
{
    /**
     * Formulaire création
     */
    public function createWeb()
    {
        $types = TypeConge::all();
        $users = \App\Models\User::where('id', '!=', auth()->id())->get(); // pour interimaire
        return view('demandes.create', compact('types', 'users'));
    }

    /**
     * Mes demandes (WEB)
     */
    public function indexWeb()
    {
        $demandes = DemandeConge::with(['type', 'interimaire'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('demandes.index', compact('demandes'));
    }

    /**
     * Stockage WEB
     */

public function storeWeb(Request $request)
{
    $data = $request->validate([
        'type_conge_id' => 'required|exists:types_conge,id',
        'date_debut'    => 'required|date|after:today',
        'date_fin'      => 'required|date|after_or_equal:date_debut',
        'lieu'          => 'required|string|max:255',
        'motif'         => 'nullable|string',
        'interimaire_id'=> 'nullable|exists:users,id',
        'piece_jointe'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    $user = auth()->user();
    $type = TypeConge::findOrFail($data['type_conge_id']);
    $days = Carbon::parse($data['date_debut'])->diffInDays(Carbon::parse($data['date_fin'])) + 1;

    /* ---------------------------------------------------------
     * Vérification congé annuel
     * --------------------------------------------------------- */
    if (strtolower(str_replace('é','e',$type->nom)) === 'conge annuel') {
        if (!in_array($days, [15, 30])) {
            return back()->withErrors([
                'date_fin' => 'La durée d\'un congé annuel doit être de 15 ou 30 jours.'
            ])->withInput();
        }
    }

    /* ---------------------------------------------------------
     * Vérification intérimaire (si non agent)
     * --------------------------------------------------------- */
    if ($user->role !== 'agent') {
        $interimaire = User::find($request->interimaire_id);

        if (
            !$interimaire ||
            $interimaire->role !== 'agent' ||
            $interimaire->direction_id !== $user->direction_id
        ) {
            return back()->with('error', 'L’intérimaire sélectionné est invalide.');
        }
    }

    /* ---------------------------------------------------------
     * Vérification congé maternité
     * --------------------------------------------------------- */
    if (strtolower(str_replace('é','e',$type->nom)) === 'conge maternite') {

        if (strtoupper($user->sexe) !== 'F') {
            return back()->withErrors([
                'type_conge_id' => 'Seules les employées de sexe féminin peuvent demander un congé maternité.'
            ])->withInput();
        }

        if ($days !== 98) {
            return back()->withErrors([
                'date_fin' => 'La durée d\'un congé de maternité doit être de 98 jours.'
            ])->withInput();
        }

        // 👍 ici seulement on exige une pièce jointe
        if (!$request->hasFile('piece_jointe')) {
            return back()->withErrors([
                'piece_jointe' => 'Un certificat de grossesse est requis pour ce type de congé.'
            ])->withInput();
        }
    }

    /* ---------------------------------------------------------
     * Vérif solde restant
     * --------------------------------------------------------- */
    $joursPris = DemandeConge::where('user_id', $user->id)
        ->where('type_conge_id', $type->id)
        ->where('statut', 'approuve_dg')
        ->get()
        ->sum(fn($d) => Carbon::parse($d->date_debut)->diffInDays(Carbon::parse($d->date_fin)) + 1);

    $soldeRestant = $type->jours_alloues - $joursPris;

    if ($days > $soldeRestant) {
        return back()->with('error', "Solde insuffisant 😕. Il vous reste seulement {$soldeRestant} jours.");
    }

    /* ---------------------------------------------------------
     * Vérif chevauchement
     * --------------------------------------------------------- */
    $existe = $user->demandes()
        ->where('statut', '!=', 'rejete')
        ->where(function ($q) use ($data) {
            $q->whereBetween('date_debut', [$data['date_debut'], $data['date_fin']])
              ->orWhereBetween('date_fin', [$data['date_debut'], $data['date_fin']]);
        })
        ->exists();

    if ($existe) {
        return back()->withErrors([
            'date_debut' => "Vous avez déjà une demande de congé en cours sur cette période."
        ])->withInput();
    }

    /* ---------------------------------------------------------
     * Création demande
     * --------------------------------------------------------- */
    $data['statut'] = 'soumis';
    $demande = $user->demandes()->create($data);

    /* ---------------------------------------------------------
     * UPLOAD IMMÉDIAT DE LA PIÈCE JOINTE
     * --------------------------------------------------------- */
    if ($request->hasFile('piece_jointe')) {
        $path = $request->file('piece_jointe')->store('pieces_jointes', 'public');
        $demande->update(['piece_jointe' => $path]);
    }



    /* ---------------------------------------------------------
     * Génération PDF
     * --------------------------------------------------------- */
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.demande', compact('demande'));
    $filename = "demande_{$demande->id}_{$user->prenom}_{$user->nom}.pdf";

    Storage::disk('public')->put("demandes/$filename", $pdf->output());
    $demande->update(['document' => "demandes/$filename"]);

    return redirect()->route('demandes.index')->with('success', 'Demande soumise avec succès ✅');
}



/**
 * 🗑️ Supprimer une demande de congé
 */
public function destroy($id)
{
    $demande = \App\Models\DemandeConge::findOrFail($id);

    // 🔒 Optionnel : empêcher la suppression si pas propriétaire ou admin
    if (auth()->id() !== $demande->user_id && auth()->user()->role !== 'admin') {
        return redirect()->back()->withErrors(['unauthorized' => "Vous n'avez pas l'autorisation de supprimer cette demande."]);
    }

    // 🧹 Supprimer le fichier joint si existe
    if ($demande->piece_jointe && \Storage::disk('public')->exists($demande->piece_jointe)) {
        \Storage::disk('public')->delete($demande->piece_jointe);
    }

    // 🧹 Supprimer le PDF généré
    if ($demande->document && \Storage::disk('public')->exists($demande->document)) {
        \Storage::disk('public')->delete($demande->document);
    }

    $demande->delete();

    return redirect()->route('demandes.index')->with('success', 'Demande supprimée avec succès ✅');
}


    /**
     * Traitement (RH / Responsable)
     */
   public function traiterWeb(Request $request)
{
    $user = auth()->user();
    $role = strtolower($user->role);

    $query = DemandeConge::with(['user.direction', 'type', 'user']);

    if ($role === 'responsable') {
        // 🔹 Responsable : uniquement demandes de sa direction (sauf maternité)
        $query->whereHas('user', fn($u) => $u->where('direction_id', $user->direction_id))
              ->whereDoesntHave('type', fn($t) => $t->where('nom', 'like', '%maternité%'));
    }

   if ($user->role === 'rh') {
            // RH : voit toutes les demandes sauf les refusées
            $demandes = DemandeConge::where('statut', '!=', 'rejete')
                ->with(['user', 'type'])
                ->latest()
                ->get();
        } 

    if ($role === 'agent' || $role === 'admin') {
        abort(403, 'Accès interdit.');
    }

    // 🔹 Filtres
    if ($request->filled('direction')) {
        $query->whereHas('user.direction', fn($q) => $q->where('id', $request->direction));
    }
    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }
    if ($request->filled('nom')) {
        $search = $request->nom;
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('nom', 'like', "%$search%")
              ->orWhere('prenom', 'like', "%$search%");
        });
    }

    $demandes = $query->latest()->paginate(15);
    $directions = Direction::all();

     //  Calcul du solde restant pour chaque demande (type concerné uniquement)
foreach ($demandes as $demande) {
    if ($demande->type && $demande->user) {
        $type = $demande->type;
        $joursPris = \App\Models\DemandeConge::where('user_id', $demande->user->id)
            ->where('type_conge_id', $type->id)
             ->whereYear('date_debut', now()->year)
            ->whereIn('statut', ['approuve_responsable', 'approuve_rh', 'approuve_dg'])
            ->get()
            ->sum(function ($d) {
                return \Carbon\Carbon::parse($d->date_debut)
                    ->diffInDays(\Carbon\Carbon::parse($d->date_fin)) + 1;
            });

        $demande->solde_restant = max(0, $type->jours_alloues - $joursPris);
    } else {
        $demande->solde_restant = null;
    }
}

    return view('demandes.traiter', compact('demandes', 'directions'));
}

  /**
     * Mettre à jour le statut
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['statut' => 'required|string']);
        $demande = DemandeConge::findOrFail($id);
        $demande->update(['statut' => $request->statut]);
        return back()->with('success', 'Statut mis à jour avec succès.');
    }

// ✅ Générer l’autorisation automatiquement (RH)

public function genererAutorisation($id)
{
    $demande = DemandeConge::findOrFail($id);

    // Génération du PDF vierge (non signé)
    $decision = Parametre::where('cle', 'decision_conge')
    ->value('valeur') ?? 'Décision N°_____';

    $pdf = Pdf::loadView('pdf.autorisation_signee', [
    'demande' => $demande,
    'decision' => $decision,
    'signaturePath' => null,
]);

    $filePath = "autorisations/autorisation_{$demande->id}.pdf";
    Storage::disk('public')->put($filePath, $pdf->output());

    // Met à jour le statut uniquement pour indiquer que le DG doit signer
    $demande->update([
        'statut' => 'en_attente_signature_dg',
        'autorisation_path' => $filePath,
    ]);

    return back()->with('success', 'Autorisation générée avec succès ✅. En attente de signature DG.');
}



public function generateAutorisationPdfWithLastSignature($id)
{
    $demande = DemandeConge::findOrFail($id);

    // 1) récupérer la liste des fichiers dans storage/app/public/signatures
    $files = Storage::disk('public')->files('signatures');

    $signatureDataUrl = null;

    if (!empty($files)) {
        // trier par date de modification (du plus récent au plus ancien)
        $last = collect($files)
            ->sortByDesc(fn($f) => Storage::disk('public')->lastModified($f))
            ->first();

        // lire le binaire
        try {
            $binary = Storage::disk('public')->get($last);
            // détecter le mime si possible (fallback png)
            $mime = 'image/png';
            try {
                $detected = Storage::disk('public')->mimeType($last);
                if (!empty($detected)) $mime = $detected;
            } catch (\Throwable $e) {
                // ignore, on garde image/png
            }

            $base64 = base64_encode($binary);
            $signatureDataUrl = "data:{$mime};base64,{$base64}";

            // (optionnel) log pour debug
            \Log::info('generateAutorisationPdfWithLastSignature: using file', ['file' => $last, 'mime' => $mime, 'len' => strlen($base64)]);
        } catch (\Throwable $e) {
            \Log::error('generateAutorisationPdfWithLastSignature: fail read', ['err' => $e->getMessage()]);
            $signatureDataUrl = null;
        }
    } else {
        \Log::info('generateAutorisationPdfWithLastSignature: no signature files found');
    }

    // 2) générer le PDF en passant la data-URI dans la vue
$decision = Parametre::where('cle', 'decision_conge')
    ->value('valeur') ?? 'Décision N°_____';

$pdf = Pdf::loadView('pdf.autorisation_signee', [
    'demande' => $demande,
    'decision' => $decision,
    'signatureDataUrl' => $signatureDataUrl,
]);


    // 3) sauvegarder le pdf et mettre à jour la demande
    $filename = "autorisations/autorisation_{$demande->id}_" . time() . ".pdf";
    Storage::disk('public')->put($filename, $pdf->output());

    $demande->update([
        'autorisation_signee_path' => $filename,
        'statut' => 'approuve_dg',
    ]);

    return redirect()->back()->with('success', 'Autorisation signée et PDF généré.');
}

//  Signature numérique (DG)

public function signerAutorisation(Request $request, $id)
{
    $demande = DemandeConge::findOrFail($id);

    $request->validate([
        'signature_data' => 'required|string',
    ]);

    // Sauvegarde la signature
    $signatureBase64 = explode(',', $request->signature_data)[1];
    $signaturePath = "signatures/signature_dg_{$demande->id}.png";
    Storage::disk('public')->put($signaturePath, base64_decode($signatureBase64));

    // Génère le PDF signé
    $decision = Parametre::where('cle', 'decision_conge')
    ->value('valeur') ?? 'Décision N°_____';

$pdf = Pdf::loadView('pdf.autorisation_signee', [
    'demande' => $demande,
    'decision' => $decision,
    'signaturePath' => storage_path("app/public/{$signaturePath}"),
]);


    $signedPath = "autorisations_signees/autorisation_signee_{$demande->id}.pdf";
    Storage::disk('public')->put($signedPath, $pdf->output());

    $demande->update([
        'statut' => 'approuve_dg',
        'autorisation_signee_path' => $signedPath,
    ]);

    return back()->with('success', 'Autorisation signée et enregistrée ✅');
}




public function regenererAutorisationSignee($id)
{
    $demande = DemandeConge::findOrFail($id);

    // Supprimer l'ancien fichier si existe
    if ($demande->autorisation_signee_path) {
        Storage::disk('public')->delete($demande->autorisation_signee_path);
    }

    // Remettre le statut en attente pour forcer signature
    $demande->update([
        'statut' => 'en_attente_signature_dg',
        'autorisation_signee_path' => null,
    ]);

    return redirect()->back()->with('success', 'Autorisation réinitialisée, vous pouvez re-signer.');
}




//  Télécharger l’autorisation signée
public function telechargerAutorisationSignee($id)
{
    $demande = DemandeConge::findOrFail($id);

    // Si le fichier n’existe pas → on le régénère automatiquement
    if (!$demande->autorisation_signee_path || !Storage::disk('public')->exists($demande->autorisation_signee_path)) {

        // 🔄 Régénération automatique
       return $this->signerAutorisationAuto($id);

    }

    // Sinon on télécharge normalement
    $prenom = strtolower(str_replace(' ', '_', $demande->user->prenom));
    $nom = strtolower(str_replace(' ', '_', $demande->user->nom));
    $date = now()->format('Ymd_His');
    $downloadName = "{$nom}_{$prenom}_{$date}.pdf";

    return Storage::disk('public')->download($demande->autorisation_signee_path, $downloadName);
}

public function signerAutorisationAuto($id)
{
    return $this->generateAutorisationPdfWithLastSignature($id);
}

// 🟢 1️⃣ Confirmer le retour de congé + génération du brouillon
    public function confirmerRetour($id)
    {
        $demande = DemandeConge::findOrFail($id);

        // Vérifie que seul le demandeur peut confirmer
        if (auth()->id() !== $demande->user_id) {
            abort(403, 'Vous ne pouvez confirmer que votre propre retour.');
        }

        // Si un certificat existe déjà, on ne régénère pas
        if ($demande->certificat_path && Storage::disk('public')->exists($demande->certificat_path)) {
            return back()->with('info', 'Un certificat est déjà en attente de signature.');
        }

        // ✅ Génération du certificat brouillon PDF
       $decision = Parametre::where('cle', 'decision_conge')
    ->value('valeur') ?? 'Décision N°_____';

$pdf = Pdf::loadView('pdf.certificat_reprise', [
    'demande' => $demande,
    'decision' => $decision,
]);

        $filename = "certificat_{$demande->id}_brouillon.pdf";
        $path = "certificats/{$filename}";

        // ✅ Sauvegarde dans le disque public
        Storage::disk('public')->put($path, $pdf->output());

        // ✅ Mise à jour de la demande
        $demande->update([
            'certificat_path' => $path,
            'statut' => 'en_attente_signature_dg', // en attente de signature
        ]);

        return back()->with('success', 'Certificat brouillon généré. En attente de signature du DG.');
    }

    // 🟡 2️⃣ Signature du certificat par le DG
   public function signerCertificat(Request $request, $id)
{
    $demande = DemandeConge::findOrFail($id);

    // ✅ Vérifie que seul le DG signe
    if (auth()->user()->role !== 'dg') {
        abort(403, 'Seul le Directeur Général peut signer le certificat.');
    }

    // ✅ Vérifie la signature reçue
    $signatureData = $request->input('signature_data');
    if (!$signatureData) {
        return back()->withErrors(['signature' => 'Signature manquante.']);
    }

    // ✅ Crée le dossier s’il n’existe pas
    if (!Storage::disk('public')->exists('certificats')) {
        Storage::disk('public')->makeDirectory('certificats');
    }

    if (!Storage::disk('public')->exists('signatures')) {
        Storage::disk('public')->makeDirectory('signatures');
    }

    // ✅ Sauvegarde la signature
    $signaturePath = "signatures/certificat_dg_{$demande->id}.png";
    $signatureImage = str_replace(['data:image/png;base64,', ' '], ['', '+'], $signatureData);
    Storage::disk('public')->put($signaturePath, base64_decode($signatureImage));

    // ✅ Génère le PDF signé
    $decision = Parametre::where('cle', 'decision_conge')
    ->value('valeur') ?? 'Décision N°_____';

    $pdf = Pdf::loadView('pdf.certificat_reprise', [
        'demande' => $demande,
        'signaturePath' => $signaturePath,
         'decision' => $decision,
    ]);

    $filename = "certificat_{$demande->id}_signe.pdf";
    $path = "certificats/{$filename}";

    Storage::disk('public')->put($path, $pdf->output());

    // ✅ Mise à jour du chemin et du statut
    $demande->update([
        'certificat_path' => $path,
        'statut' => 'termine',
         'decision' => $decision,
    ]);

    return redirect()->back()->with('success', 'Certificat signé et validé avec succès ✅');
}


    // 🔵 3️⃣ Télécharger le certificat signé
 public function telechargerCertificatSignee($id)
{
    $demande = DemandeConge::findOrFail($id);

    // Si le fichier n’existe plus → régénération automatique
    if (!$demande->certificat_path || !Storage::disk('public')->exists($demande->certificat_path)) {
        return $this->signerCertificatAuto($demande);
    }

    $prenom = strtolower(str_replace(' ', '_', $demande->user->prenom ?? ''));
    $nom = strtolower(str_replace(' ', '_', $demande->user->nom ?? ''));
    $date = now()->format('Ymd_His');
    $downloadName = "{$nom}_{$prenom}_certificat_{$date}.pdf";

    return Storage::disk('public')->download($demande->certificat_path, $downloadName);
}
private function signerCertificatAuto($demande)
{
       // Récupère les signatures dans storage/app/public/signatures
    $files = Storage::disk('public')->files('signatures');

    if (empty($files)) {
        return back()->with('error', 'Aucune signature disponible.');
    }

    // Prend la dernière signature modifiée
    $last = collect($files)
        ->sortByDesc(fn($f) => Storage::disk('public')->lastModified($f))
        ->first();

    // Récupération du fichier en binaire
    $binary = Storage::disk('public')->get($last);
    $mime = Storage::disk('public')->mimeType($last) ?? 'image/png';

    // Convertit en base64 pour DomPDF
    $b64Signature = "data:$mime;base64," . base64_encode($binary);

    // Génère le PDF
    $decision = Parametre::where('cle', 'decision_conge')
    ->value('valeur') ?? 'Décision N°_____';

    $pdf = Pdf::loadView('pdf.certificat_reprise', [
        'demande' => $demande,
        'b64Signature' => $b64Signature,
         'decision' => $decision,
    ]);

    // Enregistre proprement
    $filename = "certificat_{$demande->id}_auto.pdf";
    $path = "certificats/$filename";
    Storage::disk('public')->put($path, $pdf->output());

    // Mise à jour de la demande
    $demande->update([
        'certificat_path' => $path,
        'statut' => 'termine',
    ]);

    return Storage::disk('public')->download($path);
}


public function telechargerDemande($id)
{
    $demande = DemandeConge::findOrFail($id);

    if (!$demande->document || !Storage::disk('public')->exists($demande->document)) {
        
        // régénération
        $pdf = Pdf::loadView('pdf.demande', compact('demande'));
        $filename = "demande_{$demande->id}_regenere.pdf";

        Storage::disk('public')->put("demandes/{$filename}", $pdf->output());
        $demande->update(['document' => "demandes/{$filename}"]);
    }

    return Storage::disk('public')->download($demande->document);
}


public function historiqueWeb(Request $request)
{
    $user = auth()->user();

    // 🔎 Base de la requête : uniquement les demandes de l'utilisateur connecté
    $query = \App\Models\DemandeConge::with(['type', 'interimaire'])
        ->where('user_id', $user->id);

    //  Filtre par statut si fourni
    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }

    // Filtre par type de congé si fourni
    if ($request->filled('type_conge_id')) {
        $query->where('type_conge_id', $request->type_conge_id);
    }

    //  Filtre par plage de dates (date_debut ou date_fin)
    if ($request->filled('date_debut')) {
        $query->whereDate('date_debut', '>=', $request->date_debut);
    }
    if ($request->filled('date_fin')) {
        $query->whereDate('date_fin', '<=', $request->date_fin);
    }

    //  Récupération triée
    $demandes = $query->orderBy('created_at', 'desc')->paginate(10);

    // Pour afficher les types de congés dans un filtre dans la vue
    $types = \App\Models\TypeConge::all();

    return view('demandes.historique', compact('demandes', 'types'));
}
public function testDownload($id)
{
    $demande = \App\Models\DemandeConge::findOrFail($id);

    dd([
        'certificat_path' => $demande->certificat_path,
        'exists' => Storage::disk('public')->exists($demande->certificat_path),
        'full_path' => Storage::disk('public')->path($demande->certificat_path)
    ]);
}


}
