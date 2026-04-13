<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DemandeCongeController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ParametreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Complete routes file — toutes les routes citées dans la conversation,
| avec protection par middleware 'auth' et 'role' quand nécessaire.
|
*/

// Home -> redirect to login
Route::get('/', fn() => redirect()->route('login'));

// Auth routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerWeb'])->name('register.submit');
Route::get('/register/success', [AuthController::class, 'showSuccessPage'])->name('register.success');

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset (views / actions)
Route::get('/motdepasse/oublie', [AuthController::class, 'showForgotIdentifiantForm'])->name('password.forgot');
Route::post('/motdepasse/oublie', [AuthController::class, 'sendResetLink'])->name('password.identifiant.submit');
Route::get('/motdepasse/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/motdepasse/reset', [AuthController::class, 'resetPassword'])->name('password.reset.submit');

// Routes that require authentication
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
// Télécharger certificat signé (accessible au demandeur, RH, admin, DG, etc.)
Route::get('/telecharger-certificat/{id}', [DemandeCongeController::class, 'telechargerCertificatSignee'])
    ->name('telecharger.certificat')
    ->middleware('auth'); // tu peux remplacer par ->middleware('role:agent,rh,admin,dg') si tu veux restreindre

    /*
     |--------------------------------------------------------------------
     | Demandes (ressource-like)
     |--------------------------------------------------------------------
     | - index/create/store accessible to authenticated users (agents etc.)
     | - traitement (liste+actions) accessible selon rôle (responsable, rh, dg)
     | - certaines actions réservé au DG/admin etc.
     */
    Route::prefix('demandes')->name('demandes.')->group(function () {
        // Mes demandes / création / stockage
        Route::get('/', [DemandeCongeController::class, 'indexWeb'])->name('index');               // mes demandes
        Route::get('/create', [DemandeCongeController::class, 'createWeb'])->name('create');      // nouveau formulaire
        Route::post('/', [DemandeCongeController::class, 'storeWeb'])->name('store');             // sauvegarde

        // Historique personnel
        Route::get('/historique', [DemandeCongeController::class, 'historiqueWeb'])->name('historique');

        // Supprimer (autorisé au propriétaire / admins - verif faite dans controller)
        Route::delete('/{id}', [DemandeCongeController::class, 'destroy'])->name('destroy');

        // Actions rapides (approuver / rejeter) -> route générique patch utilisée aussi ailleurs
        Route::patch('/{id}/statut', [DemandeCongeController::class, 'updateStatus'])->name('updateStatus');

        // Génération / signature / téléchargement d'autorisations
        Route::get('/{id}/autorisation', [DemandeCongeController::class, 'genererAutorisation'])
            ->name('autorisation.generer')
            ->middleware('role:rh,admin'); // généralement RH génère l'autorisation

        Route::post('/{id}/autorisation/signer', [DemandeCongeController::class, 'signerAutorisation'])
            ->name('autorisation.signer')
            ->middleware('role:dg'); // signature numérique par DG

        Route::get('/{id}/autorisation/telecharger', [DemandeCongeController::class, 'telechargerAutorisationSignee'])
            ->name('autorisation.telecharger')
            ->middleware('role:agent,rh,responsable,admin,dg'); // qui peut telecharger selon ta logique

        // Confirmer retour (agent) -> génère certificat brouillon
        Route::post('/{id}/confirmer-retour', [DemandeCongeController::class, 'confirmerRetour'])
            ->name('confirmerRetour')
           ;

        // Signature du certificat (DG)
        Route::post('/{id}/certificat/signer', [DemandeCongeController::class, 'signerCertificat'])
            ->name('certificat.signer')
            ->middleware('role:dg');

        // Télécharger certificat signé (après DG signé) - accès au demandeur (agent) et possiblement RH
        Route::get('/{id}/certificat/telecharger', [DemandeCongeController::class, 'telechargerCertificatSignee'])
            ->name('certificat.telecharger')
            ->middleware('role:agent,rh,admin,dg,responsable');

        // Test / debug
        Route::get('/test-download/{id}', [DemandeCongeController::class, 'testDownload'])->name('test.download');

        // optionally: route qui expose events pour fullcalendar (si tu veux fetch dynamique)
        Route::get('/events/get', [DemandeCongeController::class, 'getCongeEvents'])->name('events.get');
    });

    /*
     |--------------------------------------------------------------------
     | Traitement page (liste à traiter)
     |--------------------------------------------------------------------
     | - Page /demandes/traiter accessible aux rôles qui doivent traiter:
     |   responsable, rh, dg (mais pas admin, pas agent).
     */
    Route::get('/demandes/traiter', [DemandeCongeController::class, 'traiterWeb'])
        ->name('demandes.traiter')
        ->middleware('role:responsable,rh,dg');
    Route::get('/rh/decision', [ParametreController::class, 'editDecision'])
        ->name('decision.edit')
        ->middleware('role:rh');

    Route::put('/rh/decision', [ParametreController::class, 'updateDecision'])
        ->name('decision.update')
        ->middleware('role:rh');

    /*
     |--------------------------------------------------------------------
     | Congés / Planning
     |--------------------------------------------------------------------
     */
    Route::prefix('conges')->name('conges.')->group(function () {
        Route::get('/solde', [PlanningController::class, 'solde'])->name('solde'); // accès à tous connectés
        Route::get('/planification', [PlanningController::class, 'planification'])->name('planification'); // accès à tous
        // validation page - restreindre : RH / Responsable / DG
        Route::get('/validation', [PlanningController::class, 'validation'])->name('validation')
            ->middleware('role:rh,responsable,dg');
    });

    /*
     |--------------------------------------------------------------------
     | Statistiques
     |--------------------------------------------------------------------
     */
    Route::prefix('stats')->name('stats.')->group(function () {
        Route::get('/dashboard', [StatsController::class, 'index'])->name('dashboard')
            ->middleware('role:admin,rh,responsable,dg'); // accès restreint si nécessaire

        // Rapports : interdit aux agents
        Route::get('/reports', [StatsController::class, 'reports'])->name('reports')
            ->middleware('role:admin,rh,responsable,dg');

        // Export (ex: admin/rh)
        Route::get('/export', [StatsController::class, 'export'])->name('export')
            ->middleware('role:admin,rh');
    });

    /*
     |--------------------------------------------------------------------
     | Paramètres / Users / Types / Directions
     |--------------------------------------------------------------------
     | - Accessible uniquement aux admins (sauf lecture si tu veux)
     */
    
// ✅ Paramètres
    Route::prefix('settings')->middleware('role:admin')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');

        // Utilisateurs
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/success', function () {
    return view('users.success');
})->name('users.success');

        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        // page list users (settings)
Route::get('/settings/users', [UserController::class, 'index'])->name('settings.users.index');

Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])->name('users.import');

        // Types de congés
        Route::post('/types', [TypeController::class, 'store'])->name('types.store');
        Route::put('/types/{type}', [TypeController::class, 'update'])->name('types.update');
        Route::delete('/types/{type}', [TypeController::class, 'destroy'])->name('types.destroy');

        // Directions
        Route::post('/directions', [DirectionController::class, 'store'])->name('directions.store');
        Route::put('/directions/{direction}', [DirectionController::class, 'update'])->name('directions.update');
        Route::delete('/directions/{direction}', [DirectionController::class, 'destroy'])->name('directions.destroy');
    });
    /*
     |--------------------------------------------------------------------
     | Notifications & Support
     |--------------------------------------------------------------------
     */
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/data', [NotificationController::class, 'data'])->name('notifications.data');
    Route::post('/notifications/mark-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'send'])->name('support.send');

    /*
     |--------------------------------------------------------------------
     | PDF endpoints (génération/aperçu)
     |--------------------------------------------------------------------
     */
    Route::get('/demandes/{demande}/pdf', [DemandeCongeController::class, 'genererPdf'])->name('demandes.pdf');
    Route::get('/demandes/{demande}/certificat-reprise', [DemandeCongeController::class, 'certificatReprise'])->name('demandes.certificat_reprise');
    Route::get('/demandes/{demande}/telecharger-autorisation', [DemandeCongeController::class, 'telechargerAutorisationSignee'])
    ->name('telecharger.autorisation.signee');


});
