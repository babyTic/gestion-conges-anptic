<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DemandeCongeController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Models\DemandeConge;

Route::post('/register', [AuthController::class, 'register']);
// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile utilisateur
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Demandes de congé
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/demandes', [DemandeCongeController::class, 'indexApi']);
        Route::post('/demandes', [DemandeCongeController::class, 'storeApi']);
    });


    // Planning
    Route::get('/planning', [PlanningController::class, 'index']);
    Route::get('/planning/{user}', [PlanningController::class, 'show']);
    Route::post('/planning', [PlanningController::class, 'store']);
    Route::put('/planning/{id}', [PlanningController::class, 'update']);
    Route::get('/api/conges', function () {
    return DemandeConge::with(['user', 'type'])
        ->select('id', 'user_id', 'type_conge_id', 'date_debut', 'date_fin', 'statut')
        ->get()
        ->map(function ($demande) {
            return [
                'title' => $demande->user->nom . ' ' . $demande->user->prenom . ' (' . $demande->type->nom . ')',
                'start' => $demande->date_debut,
                'end' => $demande->date_fin,
                'statut' => $demande->statut,
            ];
        });
});

    // Documents
    Route::post('/demandes/{demande}/documents', [DocumentController::class, 'store']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
    Route::get('/demandes/{demande}/documents', [DocumentController::class, 'index']);
});

Route::get('/test', function() {
    return response()->json(['status' => 'OK', 'message' => 'API fonctionne !']);
});
Route::get('/', function () {
    return response()->json(['message' => 'API ANPTIC']);
});

// Dans routes/api.php (temporairement)
Route::get('/check-user/{identifiant}', function($identifiant) {
    $user = App\Models\User::where('identifiant', $identifiant)->first();
    return response()->json($user ?? ['error' => 'Utilisateur non trouvé']);
});

// Route temporaire pour vérifier le hash
Route::get('/check-password', function() {
    $user = App\Models\User::where('identifiant', 'ADM901')->first();
    $password = 'password';
    $isValid = Hash::check($password, $user->password);

    return response()->json([
        'user_exists' => !!$user,
        'password_match' => $isValid,
        'password_hash' => $user ? $user->password : null
    ]);
});

// Route de fallback pour les URLs non trouvées
Route::fallback(function() {
    return response()->json(['error' => 'Route API non trouvée'], 404);
});

// Gardez VOS routes API (elles sont différentes)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum'); // ✅ POUR L'API
