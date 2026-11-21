<?php

use App\Http\Controllers\Auth\AgencyRegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\FamilleDeBienController;
use App\Http\Controllers\UniteController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\LocataireController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisiteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Agence\ParametreController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TeamSetupController;
use App\Http\Controllers\TeamManagementController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Inscription Agence
Route::get('/inscription-agence', [AgencyRegisterController::class, 'showRegistrationForm'])->name('register.agency');
Route::post('/inscription-agence', [AgencyRegisterController::class, 'register'])->name('register.agency.post');

// ============================================================================
// ROUTES SETUP ÉQUIPE (après inscription)
// ============================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/team-setup/choose-mode', [TeamSetupController::class, 'chooseMode'])->name('team-setup.choose-mode');
    Route::post('/team-setup/store-mode', [TeamSetupController::class, 'storeMode'])->name('team-setup.store-mode');
    Route::get('/team-setup/select-modules', [TeamSetupController::class, 'selectModules'])->name('team-setup.select-modules');
    Route::post('/team-setup/assign-modules', [TeamSetupController::class, 'assignModules'])->name('team-setup.assign-modules');
    Route::post('/team-setup/review', [TeamSetupController::class, 'review'])->name('team-setup.review');
    Route::post('/team-setup/finalize', [TeamSetupController::class, 'finalize'])->name('team-setup.finalize');
});


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// ============================================================================
// ROUTES PROTÉGÉES PAR MODULES (Admin + Team Members)
// ============================================================================
// ROUTES PROTÉGÉES PAR MODULES (Admin + Team Members)
// ============================================================================

// MODULE IMMOBILIER
Route::middleware(['auth', 'role:admin|team_member', 'module:immobilier,collaborator'])->group(function () {
    // Création/Modification (collaborator+) -- Déclaré EN PREMIER pour éviter le conflit avec {familleDeBien}
    Route::get('/proprietes/create', [FamilleDeBienController::class, 'create'])->name('proprietes.create');
    Route::post('/proprietes', [FamilleDeBienController::class, 'store'])->name('proprietes.store');
    Route::get('/proprietes/{familleDeBien}/edit', [FamilleDeBienController::class, 'edit'])->name('proprietes.edit');
    Route::match(['put', 'patch'], '/proprietes/{familleDeBien}', [FamilleDeBienController::class, 'update'])->name('proprietes.update');

    // Rétrocompatibilité liens /familles-de-biens
    Route::get('/familles-de-biens/create', fn() => redirect()->route('proprietes.create', request()->query()))->name('familles-de-biens.create');
    Route::post('/familles-de-biens', [FamilleDeBienController::class, 'store'])->name('familles-de-biens.store');
    Route::get('/familles-de-biens/{familleDeBien}/edit', fn($familleDeBien) => redirect()->route('proprietes.edit', $familleDeBien))->name('familles-de-biens.edit');
    Route::match(['put', 'patch'], '/familles-de-biens/{familleDeBien}', [FamilleDeBienController::class, 'update'])->name('familles-de-biens.update');

    Route::get('/biens/create', [BienController::class, 'create'])->name('biens.create');
    Route::post('/biens', [BienController::class, 'store'])->name('biens.store');
    Route::get('/biens/{bien}/edit', [BienController::class, 'edit'])->name('biens.edit');
    Route::match(['put', 'patch'], '/biens/{bien}', [BienController::class, 'update'])->name('biens.update');
    Route::post('/biens/{bien}/subbiens', [BienController::class, 'storeSubBien'])->name('biens.subbiens.store');
    Route::get('/subbiens/{bien}/edit', [BienController::class, 'editSubBien'])->name('biens.subbiens.edit');
    Route::match(['put', 'patch'], '/subbiens/{bien}', [BienController::class, 'updateSubBien'])->name('biens.subbiens.update');
    Route::resource('unites', UniteController::class)->only(['store', 'update']);
});

Route::middleware(['auth', 'role:admin|team_member', 'module:immobilier,viewer'])->group(function () {
    // Consultation (viewer+)
    Route::get('/proprietes', [FamilleDeBienController::class, 'index'])->name('proprietes.index');
    Route::get('/proprietes/{familleDeBien}', [FamilleDeBienController::class, 'show'])->name('proprietes.show');
    Route::get('/proprietes-imprimer', [FamilleDeBienController::class, 'printList'])->name('proprietes.print');

    // Rétrocompatibilité liens /familles-de-biens
    Route::get('/familles-de-biens', fn() => redirect()->route('proprietes.index', request()->query()))->name('familles-de-biens.index');
    Route::get('/familles-de-biens/{familleDeBien}', fn($familleDeBien) => redirect()->route('proprietes.show', $familleDeBien))->name('familles-de-biens.show');
    Route::get('/familles-de-biens-imprimer', fn() => redirect()->route('proprietes.print', request()->query()))->name('familles-de-biens.print');

    Route::get('/biens', [BienController::class, 'index'])->name('biens.index');
    Route::get('/biens/{bien}', [BienController::class, 'show'])->name('biens.show');
});

Route::middleware(['auth', 'role:admin|team_member', 'module:immobilier,manager'])->group(function () {
    // Suppression (manager only)
    Route::delete('/proprietes/{familleDeBien}', [FamilleDeBienController::class, 'destroy'])->name('proprietes.destroy');
    Route::delete('/familles-de-biens/{familleDeBien}', [FamilleDeBienController::class, 'destroy'])->name('familles-de-biens.destroy');
    Route::delete('/biens/{bien}', [BienController::class, 'destroy'])->name('biens.destroy');
    Route::delete('/subbiens/{bien}', [BienController::class, 'destroySubBien'])->name('biens.subbiens.destroy');
    Route::delete('/unites/{unite}', [UniteController::class, 'destroy'])->name('unites.destroy');
});

// MODULE COMMERCIAL
Route::middleware(['auth', 'role:admin|team_member', 'module:commercial,collaborator'])->group(function () {
    // Création/Modification (collaborator+) -- Déclaré EN PREMIER pour éviter le conflit avec {locataire}
    Route::get('/visites/create', [VisiteController::class, 'create'])->name('visites.create');
    Route::post('/visites', [VisiteController::class, 'store'])->name('visites.store');
    Route::patch('/visites/{visite}/completed', [VisiteController::class, 'markAsCompleted'])->name('visites.completed');
    Route::get('/locataires/create', [LocataireController::class, 'create'])->name('locataires.create');
    Route::post('/locataires', [LocataireController::class, 'store'])->name('locataires.store');
    Route::get('/locataires/{locataire}/edit', [LocataireController::class, 'edit'])->name('locataires.edit');
    Route::match(['put', 'patch'], '/locataires/{locataire}', [LocataireController::class, 'update'])->name('locataires.update');
    Route::post('/locataires/{locataire}/audit', [LocataireController::class, 'audit'])->name('locataires.audit');
    Route::get('/contrats/create', [ContratController::class, 'create'])->name('contrats.create');
    Route::post('/contrats', [ContratController::class, 'store'])->name('contrats.store');
    Route::get('/contrats/{contrat}/etat-des-lieux', [ContratController::class, 'etatDesLieux'])->name('contrats.etat-des-lieux');
    Route::post('/contrats/{contrat}/etat-des-lieux', [ContratController::class, 'storeEtatDesLieux'])->name('contrats.etat-des-lieux.store');
    Route::post('/contrats/{contrat}/etat-des-lieux-sortie', [ContratController::class, 'storeEtatDesLieuxSortie'])->name('contrats.etat-des-lieux-sortie.store'); // Clôture contrat - admin/team only
    // Note: GET etat-des-lieux-sortie routes moved to shared routes to allow proprietaire viewing
    Route::get('/contrats/{contrat}/edit', [ContratController::class, 'edit'])->name('contrats.edit');
    Route::match(['put', 'patch'], '/contrats/{contrat}', [ContratController::class, 'update'])->name('contrats.update');
    Route::patch('/contrats/{contrat}/inventory', [ContratController::class, 'updateInventory'])->name('contrats.update-inventory');
    Route::patch('/contrats/{contrat}/notice', [ContratController::class, 'registerNotice'])->name('contrats.register-notice');
});

Route::middleware(['auth', 'role:admin|team_member', 'module:commercial,viewer'])->group(function () {
    // Consultation (viewer+)
    Route::get('/visites', [VisiteController::class, 'index'])->name('visites.index');
    Route::get('/locataires', [LocataireController::class, 'index'])->name('locataires.index');
    Route::get('/locataires/{locataire}', [LocataireController::class, 'show'])->name('locataires.show');
    Route::get('/locataires-imprimer', [LocataireController::class, 'printList'])->name('locataires.print');
    Route::get('/contrats', [ContratController::class, 'index'])->name('contrats.index');
    // Note: contrats.show moved to shared routes to allow proprietaire access
});

Route::middleware(['auth', 'role:admin|team_member', 'module:commercial,manager'])->group(function () {
    // Suppression et actions sensibles (manager only)
    Route::patch('/visites/{visite}/cancel', [VisiteController::class, 'cancel'])->name('visites.cancel');
    Route::delete('/visites/{visite}', [VisiteController::class, 'destroy'])->name('visites.destroy');
    Route::delete('/locataires/{locataire}', [LocataireController::class, 'destroy'])->name('locataires.destroy');
    Route::patch('/contrats/{contrat}/close', [ContratController::class, 'close'])->name('contrats.close');
    Route::delete('/contrats/{contrat}', [ContratController::class, 'destroy'])->name('contrats.destroy');
});

// MODULE FINANCE
Route::middleware(['auth', 'role:admin|team_member', 'module:finance,viewer'])->group(function () {
    // Consultation (viewer+)
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('/factures-imprimer', [FactureController::class, 'printList'])->name('factures.print');
    Route::get('/agence/rapport-financier', [DashboardController::class, 'agencyFinanceReport'])->name('agency.finance-report');
});

Route::middleware(['auth', 'role:admin|team_member', 'module:finance,manager'])->group(function () {
    // Gestion financière (manager only - sensible)
    Route::post('/factures', [FactureController::class, 'store'])->name('factures.store');
    Route::post('/factures/{facture}/payer', [FactureController::class, 'recordPayment'])->name('factures.payer');
    Route::post('/factures/force-generation', [FactureController::class, 'forceGeneration'])->name('factures.force_generation');
    Route::post('/finance/contrats/{contrat}/rembourser-caution', [FinanceController::class, 'rembourserCaution'])->name('finance.rembourser-caution');
    Route::post('/finance/proprietaires/{proprietaire}/reversement', [FinanceController::class, 'creerReversement'])->name('finance.creer-reversement');
    Route::post('/finance/reversements/batch', [FinanceController::class, 'creerReversementsBatch'])->name('finance.reversements.batch');
    Route::delete('/finance/reversements/{reversement}', [FinanceController::class, 'supprimerReversement'])->name('finance.supprimer-reversement');
});

// MODULE PROPRIÉTAIRES
Route::middleware(['auth', 'role:admin|team_member', 'module:proprietaires,collaborator'])->group(function () {
    // Création/Modification (collaborator+) -- Déclaré EN PREMIER pour éviter le conflit avec {proprietaire}
    Route::get('/proprietaires/create', [ProprietaireController::class, 'create'])->name('proprietaires.create');
    Route::post('/proprietaires', [ProprietaireController::class, 'store'])->name('proprietaires.store');
    Route::get('/proprietaires/{proprietaire}/edit', [ProprietaireController::class, 'edit'])->name('proprietaires.edit');
    Route::match(['put', 'patch'], '/proprietaires/{proprietaire}', [ProprietaireController::class, 'update'])->name('proprietaires.update');
});

Route::middleware(['auth', 'role:admin|team_member', 'module:proprietaires,viewer'])->group(function () {
    // Consultation (viewer+)
    Route::get('/proprietaires', [ProprietaireController::class, 'index'])->name('proprietaires.index');
    Route::get('/proprietaires/{proprietaire}', [ProprietaireController::class, 'show'])->name('proprietaires.show');
    Route::get('/proprietaires-imprimer', [ProprietaireController::class, 'printList'])->name('proprietaires.print');
});

Route::middleware(['auth', 'role:admin|team_member', 'module:proprietaires,manager'])->group(function () {
    // Suppression (manager only)
    Route::delete('/proprietaires/{proprietaire}', [ProprietaireController::class, 'destroy'])->name('proprietaires.destroy');
});


// ============================================================================
// ROUTES ADMIN UNIQUEMENT (Configuration, Team, etc.)
// ============================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Configuration Agence (Admin uniquement)
    Route::get('/agence/parametres', [ParametreController::class, 'index'])->name('agence.parametres');
    Route::patch('/agence/parametres', [ParametreController::class, 'update'])->name('agence.parametres.update');
    Route::patch('/agence/parametres/paiement', [ParametreController::class, 'updatePaiement'])->name('agence.parametres.update_paiement');
    Route::post('/agence/comptes', [ParametreController::class, 'addCompteBancaire'])->name('agence.comptes.add');
    Route::delete('/agence/comptes/{compte}', [ParametreController::class, 'deleteCompteBancaire'])->name('agence.comptes.delete');

    // Gestion de l'équipe (Admin uniquement)
    Route::get('/team', [TeamManagementController::class, 'index'])->name('team.index');
    Route::get('/team/create', [TeamManagementController::class, 'create'])->name('team.create');
    Route::post('/team', [TeamManagementController::class, 'store'])->name('team.store');
    Route::get('/team/{teamMember}/edit', [TeamManagementController::class, 'edit'])->name('team.edit');
    Route::patch('/team/{teamMember}', [TeamManagementController::class, 'update'])->name('team.update');
    Route::delete('/team/{teamMember}', [TeamManagementController::class, 'destroy'])->name('team.destroy');
    Route::get('/team/{teamMember}/reset-password', [TeamManagementController::class, 'showResetPassword'])->name('team.reset-password');
    Route::post('/team/{teamMember}/reset-password', [TeamManagementController::class, 'resetPassword'])->name('team.reset-password.update');
    Route::post('/team/{teamMember}/suspend', [TeamManagementController::class, 'suspend'])->name('team.suspend');
    Route::post('/team/{teamMember}/reactivate', [TeamManagementController::class, 'reactivate'])->name('team.reactivate');
    Route::get('/team/{teamMember}/history', [TeamManagementController::class, 'history'])->name('team.history');
    Route::post('/team/switch-to-team-mode', [TeamManagementController::class, 'switchToTeamMode'])->name('team.switch-to-team-mode');
    Route::post('/team/switch-to-solo-mode', [TeamManagementController::class, 'switchToSoloMode'])->name('team.switch-to-solo-mode');
});

// ============================================================================
// ROUTES PARTAGÉES (Accessibles par tous mais contrôlées dans les controllers)
// ============================================================================
Route::middleware('auth')->group(function () {
    // Visites (consultation pour tous)
    Route::get('/visites/{visite}/recu', [VisiteController::class, 'generateReceipt'])->name('visites.recu');

    // Contrats (consultation autorisée pour locataires, propriétaires, admin, team_member)
    Route::get('/mes-contrats', [ContratController::class, 'mesContrats'])->name('contrats.mes-contrats');
    Route::get('/contrats/imprimer', [ContratController::class, 'printList'])->name('contrats.print');
    Route::get('/contrats/{contrat}', [ContratController::class, 'show'])->name('contrats.show'); // Access control in controller
    Route::get('/contrats/{contrat}/bail', [ContratController::class, 'showBail'])->name('contrats.bail');
    Route::get('/contrats/{contrat}/etat-des-lieux-print', [ContratController::class, 'printEtatDesLieux'])->name('contrats.etat-des-lieux.print');
    Route::get('/contrats/{contrat}/etat-des-lieux-sortie', [ContratController::class, 'etatDesLieuxSortie'])->name('contrats.etat-des-lieux-sortie'); // Proprietaires can view
    Route::get('/contrats/{contrat}/etat-des-lieux-sortie-print', [ContratController::class, 'printEtatDesLieuxSortie'])->name('contrats.etat-des-lieux-sortie.print');

    // Factures (consultation pour locataires et propriétaires)
    Route::get('/mes-factures', [FactureController::class, 'mesFactures'])->name('factures.mes-factures');
    Route::get('/factures/{facture}/recu', [FactureController::class, 'generateReceipt'])->name('factures.recu');
    
    // Propriétés (consultation pour propriétaires)
    Route::get('/mes-proprietes', [ProprietaireController::class, 'mesProprietes'])->name('proprietaires.mes-proprietes');
    
    // Rapports financiers (locataires et propriétaires)
    Route::get('/locataire/rapport-financier', [DashboardController::class, 'tenantFinanceReport'])->name('tenant.finance-report');
    Route::get('/proprietaire/rapport-financier', [DashboardController::class, 'ownerFinanceReport'])->name('owner.finance-report');

    // Messages / Messagerie (Tous)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');

    // Notifications (Tous)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    // Documents privés et sécurisés (CNI, pièces d'identité)
    Route::get('/documents/locataire/{locataire}/{type}', [DocumentController::class, 'locatairePiece'])->name('documents.locataire');
    Route::get('/documents/proprietaire/{proprietaire}/{type}', [DocumentController::class, 'proprietairePiece'])->name('documents.proprietaire');

    // Profile (Tous)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
