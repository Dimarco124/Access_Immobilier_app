<?php

use App\Http\Controllers\Auth\AgencyRegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\UniteController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\LocataireController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisiteController;
use App\Http\Controllers\Agence\ParametreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Inscription Agence
Route::get('/inscription-agence', [AgencyRegisterController::class, 'showRegistrationForm'])->name('register.agency');
Route::post('/inscription-agence', [AgencyRegisterController::class, 'register'])->name('register.agency.post');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Biens principaux
    Route::resource('biens', BienController::class);
    
    // Sous-biens
    Route::post('/biens/{bien}/subbiens', [BienController::class, 'storeSubBien'])->name('biens.subbiens.store');
    Route::get('/subbiens/{bien}/edit', [BienController::class, 'editSubBien'])->name('biens.subbiens.edit');
    Route::patch('/subbiens/{bien}', [BienController::class, 'updateSubBien'])->name('biens.subbiens.update');
    Route::delete('/subbiens/{bien}', [BienController::class, 'destroySubBien'])->name('biens.subbiens.destroy');

    Route::resource('unites', UniteController::class)->only(['store', 'update', 'destroy']);

    // Visites
    Route::get('/visites', [VisiteController::class, 'index'])->name('visites.index');
    Route::get('/visites/create', [VisiteController::class, 'create'])->name('visites.create');
    Route::post('/visites', [VisiteController::class, 'store'])->name('visites.store');
    Route::patch('/visites/{visite}/completed', [VisiteController::class, 'markAsCompleted'])->name('visites.completed');
    Route::patch('/visites/{visite}/cancel', [VisiteController::class, 'cancel'])->name('visites.cancel');
    Route::delete('/visites/{visite}', [VisiteController::class, 'destroy'])->name('visites.destroy');

    // Contrats et Factures
    Route::resource('contrats', ContratController::class);
    Route::get('/mes-contrats', [ContratController::class, 'mesContrats'])->name('contrats.mes-contrats');
    Route::get('/factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('/mes-factures', [FactureController::class, 'mesFactures'])->name('factures.mes-factures');
    Route::post('/factures', [FactureController::class, 'store'])->name('factures.store');
    Route::post('/factures/{facture}/payer', [FactureController::class, 'recordPayment'])->name('factures.payer');

    // Propriétaires
    Route::resource('proprietaires', ProprietaireController::class);

    // Locataires
    Route::resource('locataires', LocataireController::class);

    // Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/message', [TicketController::class, 'addMessage'])->name('tickets.message');
    Route::patch('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');

    // Paramètres Agence
    Route::get('/agence/parametres', [ParametreController::class, 'index'])->name('agence.parametres');
    Route::patch('/agence/parametres', [ParametreController::class, 'update'])->name('agence.parametres.update');
    Route::post('/agence/comptes', [ParametreController::class, 'addCompteBancaire'])->name('agence.comptes.add');
    Route::delete('/agence/comptes/{compte}', [ParametreController::class, 'deleteCompteBancaire'])->name('agence.comptes.delete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
