<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DemandeConge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Téléverse un document pour une demande de congé
     */
    public function store(Request $request, DemandeConge $demande)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'type' => 'required|in:justificatif,certificat,autre'
        ]);

        // Vérification des permissions
        $this->authorize('upload', [Document::class, $demande]);

        $file = $request->file('fichier');
        $filename = Str::uuid() . '.' . $file->extension();

        // Stockage sécurisé dans storage/app/documents
        $path = $file->storeAs('documents', $filename);

        $document = $demande->documents()->create([
            'nom_original' => $file->getClientOriginalName(),
            'chemin' => $path,
            'type' => $request->type,
            'taille' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);

        return response()->json([
            'message' => 'Document téléversé avec succès',
            'document' => $document
        ], 201);
    }

    /**
     * Télécharge un document
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);

        if (!Storage::exists($document->chemin)) {
            abort(404);
        }

        return Storage::download(
            $document->chemin,
            $document->nom_original,
            ['Content-Type' => $document->mime_type]
        );
    }

    /**
     * Supprime un document
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        Storage::delete($document->chemin);
        $document->delete();

        return response()->json([
            'message' => 'Document supprimé avec succès'
        ]);
    }

    /**
     * Liste les documents d'une demande
     */
    public function index(DemandeConge $demande)
    {
        $this->authorize('viewAny', [Document::class, $demande]);

        return $demande->documents()->get();
    }


public function updateDecision(Request $request)
{
    $request->validate([
        'decision' => 'required|string|max:255'
    ]);

    $document = Document::first();

    if (!$document) {
        $document = Document::create([
            'decision' => $request->decision
        ]);
    } else {
        $document->update([
            'decision' => $request->decision
        ]);
    }

    return back()->with('success', 'Décision mise à jour avec succès ✅');
}


}
