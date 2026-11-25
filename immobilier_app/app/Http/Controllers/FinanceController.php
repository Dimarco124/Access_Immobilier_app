<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use App\Models\CompteBancaire;
use App\Models\Contrat;
use App\Models\Depense;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\Reversement;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $agence = Auth::user()->agence;

        // Filtre par mois (même logique que Dashboard)
        $moisSelectionne = $request->mois;
        if ($moisSelectionne) {
            $dateFiltre = Carbon::parse($moisSelectionne);
        } else {
            $dateFiltre = Carbon::now();
        }
        $moisFormat = $dateFiltre->translatedFormat('F Y');
        $moisInputValue = $dateFiltre->format('Y-m');

        // Générer la liste des mois (derniers 24 mois)
        $listeMois = [];
        for ($i = 0; $i < 24; $i++) {
            $d = Carbon::now()->subMonths($i);
            $listeMois[] = [
                'value' => $d->format('Y-m'),
                'label' => $d->translatedFormat('F Y')
            ];
        }

        // 1. GESTION DES CAUTIONS (uniquement celles réellement encaissées)
        $cautions = Contrat::where('agence_id', $agence->id)
            ->with(['locataire.user', 'bien.familleDeBien', 'factures'])
            ->get()
            ->map(function ($contrat) {
                $factureInitiale = $contrat->factures->first(function ($f) {
                    return in_array($f->type_facture, ['Initial', 'Caution']);
                });

                if ($factureInitiale) {
                    if ($factureInitiale->statut === 'Payée') {
                        $cautionRecue = (float)$contrat->montant_caution;
                    } elseif ($factureInitiale->montant_deja_paye > 0 && $factureInitiale->montant_total_du > 0) {
                        $ratio = (float)$contrat->montant_caution / (float)$factureInitiale->montant_total_du;
                        $cautionRecue = min((float)$contrat->montant_caution, (float)$factureInitiale->montant_deja_paye * $ratio);
                    } else {
                        // Facture non payée => 0 caution détenue
                        $cautionRecue = 0;
                    }
                } else {
                    $cautionRecue = 0;
                }

                $contrat->caution_payee = $cautionRecue;
                $contrat->caution_disponible = max(0, $cautionRecue - (float)$contrat->montant_caution_rembourse - (float)$contrat->montant_caution_retenu);
                return $contrat;
            });

        $totalCautions = $cautions->sum('caution_payee');
        $totalCautionsDisponibles = $cautions->sum('caution_disponible');

        // 2. SOLDE DES PROPRIÉTAIRES
        $proprietaires = Proprietaire::where('agence_id', $agence->id)
            ->with(['user', 'biens'])
            ->get()
            ->map(function ($proprietaire) use ($dateFiltre, $agence, $moisFormat) {
                // Calculer les loyers collectés pour ce propriétaire sur le mois
                $loyersCollectes = 0;
                $commissions = 0;

                // 1. Loyers des factures de type Loyer
                $paiementsLoyerProprio = Paiement::where('paiements.agence_id', $agence->id)
                    ->whereHas('facture.contrat.bien', function ($q) use ($proprietaire) {
                        $q->where('proprietaire_id', $proprietaire->id);
                    })
                    ->whereHas('facture', function ($q) use ($moisFormat) {
                        $q->whereRaw('LOWER(mois_facture) = ?', [mb_strtolower($moisFormat)])
                          ->where('type_facture', 'Loyer');
                    })
                    ->join('factures', 'paiements.facture_id', '=', 'factures.id')
                    ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
                    ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                    ->select('paiements.montant_verse', 'biens.taux_commission_agence')
                    ->get();

                foreach ($paiementsLoyerProprio as $pLoyer) {
                    $loyersCollectes += $pLoyer->montant_verse;
                    $commissions += $pLoyer->montant_verse * ($pLoyer->taux_commission_agence / 100);
                }

                // 2. Loyers depuis les factures Initiales pour ce propriétaire
                $facturesInitialesProprio = Paiement::where('paiements.agence_id', $agence->id)
                    ->whereHas('facture.contrat.bien', function ($q) use ($proprietaire) {
                        $q->where('proprietaire_id', $proprietaire->id);
                    })
                    ->whereHas('facture', function ($q) use ($moisFormat) {
                        $q->whereRaw('LOWER(mois_facture) = ?', [mb_strtolower($moisFormat)])
                          ->where('type_facture', 'Initial');
                    })
                    ->join('factures', 'paiements.facture_id', '=', 'factures.id')
                    ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
                    ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                    ->select(
                        'factures.montant_loyer_initial',
                        'factures.montant_total_du',
                        'paiements.montant_verse',
                        'biens.taux_commission_agence'
                    )
                    ->get();

                foreach ($facturesInitialesProprio as $paiement) {
                    if ($paiement->montant_total_du > 0 && $paiement->montant_loyer_initial > 0) {
                        $proportion = $paiement->montant_loyer_initial / $paiement->montant_total_du;
                        $montantLoyerPaye = $paiement->montant_verse * $proportion;
                        $loyersCollectes += $montantLoyerPaye;
                        $commissions += $montantLoyerPaye * ($paiement->taux_commission_agence / 100);
                    }
                }

                // Calculer les dépenses imputées sur ce mois
                $depenses = Depense::where('depenses.agence_id', $agence->id)
                    ->whereHas('bien', function ($q) use ($proprietaire) {
                        $q->where('proprietaire_id', $proprietaire->id);
                    })
                    ->whereMonth('depenses.date_depense', $dateFiltre->month)
                    ->whereYear('depenses.date_depense', $dateFiltre->year)
                    ->sum('depenses.montant');

                // Calculer les montants déjà reversés au propriétaire sur cette période
                $reversementsEffectues = Reversement::where('proprietaire_id', $proprietaire->id)
                    ->where(function ($q) use ($dateFiltre, $moisFormat) {
                        $q->whereRaw('LOWER(periode) = ?', [mb_strtolower($moisFormat)])
                          ->orWhere(function ($sub) use ($dateFiltre) {
                              $sub->whereMonth('date_reversement', $dateFiltre->month)
                                  ->whereYear('date_reversement', $dateFiltre->year);
                          });
                    })
                    ->sum('montant_net_verse');

                // Calculer le solde à reverser
                $soldeProprietaire = max(0, $loyersCollectes - $commissions - $depenses - $reversementsEffectues);

                $proprietaire->loyers_collectes = $loyersCollectes;
                $proprietaire->reversements_effectues = $reversementsEffectues;
                $proprietaire->commissions = $commissions;
                $proprietaire->depenses = $depenses;
                $proprietaire->solde = $soldeProprietaire;

                return $proprietaire;
            });

        // Get all reversements for history
        $reversements = Reversement::where('agence_id', $agence->id)
            ->with(['proprietaire.user', 'compteBancaire'])
            ->orderBy('date_reversement', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 3. COMPTE AGENCE
        // Commissions sur loyers classiques (factures type Loyer)
        $commissionsLoyerClassique = Paiement::where('paiements.agence_id', $agence->id)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->whereRaw('LOWER(factures.mois_facture) = ?', [mb_strtolower($moisFormat)])
            ->where('factures.type_facture', 'Loyer')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->selectRaw('SUM(paiements.montant_verse * (biens.taux_commission_agence / 100)) as total')
            ->value('total') ?? 0;

        // Commissions sur la partie loyer des factures Initiales
        $commissionsLoyerInitial = 0;
        $facturesInitialesCommissions = Paiement::where('paiements.agence_id', $agence->id)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->whereRaw('LOWER(factures.mois_facture) = ?', [mb_strtolower($moisFormat)])
            ->where('factures.type_facture', 'Initial')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->select(
                'factures.montant_loyer_initial',
                'factures.montant_total_du',
                'paiements.montant_verse',
                'biens.taux_commission_agence'
            )
            ->get();

        foreach ($facturesInitialesCommissions as $paiementInit) {
            if ($paiementInit->montant_total_du > 0 && $paiementInit->montant_loyer_initial > 0) {
                $prop = $paiementInit->montant_loyer_initial / $paiementInit->montant_total_du;
                $loyerPaye = $paiementInit->montant_verse * $prop;
                $commissionsLoyerInitial += $loyerPaye * ($paiementInit->taux_commission_agence / 100);
            }
        }

        $commissionsLoyers = round($commissionsLoyerClassique + $commissionsLoyerInitial, 2);

        // Frais de dossier et visites
        $fraisDossier = 0;

        // 1. Frais Dossier factures séparées
        $fraisDossier += Paiement::where('paiements.agence_id', $agence->id)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->whereRaw('LOWER(factures.mois_facture) = ?', [mb_strtolower($moisFormat)])
            ->where('factures.type_facture', 'Frais Dossier')
            ->sum('paiements.montant_verse');

        // 2. Frais Dossier depuis les factures Initiales (basé sur le champ frais_dossier de la facture)
        $facturesInitiales = Paiement::where('paiements.agence_id', $agence->id)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->whereRaw('LOWER(factures.mois_facture) = ?', [mb_strtolower($moisFormat)])
            ->where('factures.type_facture', 'Initial')
            ->select('factures.id', 'factures.frais_dossier', 'factures.montant_caution', 'factures.montant_loyer_initial', 'factures.montant_total_du', 'factures.mois_facture', 'paiements.montant_verse')
            ->get();

        foreach ($facturesInitiales as $paiement) {
            if ($paiement->montant_total_du > 0 && $paiement->frais_dossier > 0) {
                $proportion = $paiement->frais_dossier / $paiement->montant_total_du;
                $fraisDossier += $paiement->montant_verse * $proportion;
            }
        }
        $visites = \App\Models\Visite::where('agence_id', $agence->id)
            ->whereMonth('created_at', $dateFiltre->month)
            ->whereYear('created_at', $dateFiltre->year)
            ->sum('montant_paye');
        $retenuesCaution = Transaction::where('agence_id', $agence->id)
            ->where('type_transaction', 'retenue_caution')
            ->whereMonth('date_transaction', $dateFiltre->month)
            ->whereYear('date_transaction', $dateFiltre->year)
            ->sum('montant');

        $revenusAgence = $commissionsLoyers + $fraisDossier + $visites + $retenuesCaution;

        // Dépenses de l'agence sur le mois
        $depensesAgence = Depense::where('agence_id', $agence->id)
            ->whereMonth('date_depense', $dateFiltre->month)
            ->whereYear('date_depense', $dateFiltre->year)
            ->sum('montant');

        $soldeAgence = $revenusAgence - $depensesAgence;
        $tauxMargeAgence = ($revenusAgence > 0) ? round(($soldeAgence / $revenusAgence) * 100, 1) : 0;

        // 4. STATISTIQUES AVANCÉES DE RECOUVREMENT DES LOYERS DU MOIS
        $facturesMois = Facture::where('agence_id', $agence->id)
            ->whereRaw('LOWER(mois_facture) = ?', [mb_strtolower($moisFormat)])
            ->get();

        $loyersEmis = 0;
        $loyersEncaisses = 0;
        $nbFacturesPayees = 0;
        $nbFacturesPartielles = 0;
        $nbFacturesImpayees = 0;

        foreach ($facturesMois as $f) {
            if ($f->statut === 'Payée') {
                $nbFacturesPayees++;
            } elseif ($f->statut === 'Partielle') {
                $nbFacturesPartielles++;
            } else {
                $nbFacturesImpayees++;
            }

            if ($f->type_facture === 'Loyer') {
                $loyersEmis += $f->montant_total_du;
                $loyersEncaisses += $f->montant_deja_paye;
            } elseif ($f->type_facture === 'Initial') {
                if ($f->montant_total_du > 0 && $f->montant_loyer_initial > 0) {
                    $prop = $f->montant_loyer_initial / $f->montant_total_du;
                    $loyersEmis += $f->montant_loyer_initial;
                    $loyersEncaisses += $f->montant_deja_paye * $prop;
                }
            }
        }

        $resteARecouvrer = max(0, $loyersEmis - $loyersEncaisses);
        $tauxRecouvrement = $loyersEmis > 0 ? round(($loyersEncaisses / $loyersEmis) * 100, 1) : 100;
        $nbFacturesTotal = $facturesMois->count();

        // 5. TRÉSORERIE PROPRIÉTAIRES & FONDS DE TIERS
        $totalReversementsMois = $proprietaires->sum('reversements_effectues');
        $totalSoldeEnAttente = $proprietaires->where('solde', '>', 0)->sum('solde');
        $proprietairesEnAttenteCount = $proprietaires->where('solde', '>', 0)->count();

        // 6. MODES DE PAIEMENT DU MOIS (Canaux d'encaissement)
        $paiementsModes = Paiement::where('agence_id', $agence->id)
            ->whereMonth('date_paiement', $dateFiltre->month)
            ->whereYear('date_paiement', $dateFiltre->year)
            ->selectRaw('COALESCE(mode_paiement, "Autre") as mode, SUM(montant_verse) as total, COUNT(*) as nb')
            ->groupBy('mode')
            ->orderByDesc('total')
            ->get();
        $totalPaiementsMois = $paiementsModes->sum('total');

        // 7. CESSIONS & SÉQUESTRE DES CAUTIONS
        $totalCautionsRemboursees = $cautions->sum('montant_caution_rembourse');
        $totalCautionsRetenues = $cautions->sum('montant_caution_retenu');

        // 8. PARC LOCATIF ACTIF
        $valeurLocativeActive = Contrat::where('agence_id', $agence->id)
            ->where('statut', 'Actif')
            ->sum('montant_loyer_actuel');
        $contratsActifsCount = Contrat::where('agence_id', $agence->id)
            ->where('statut', 'Actif')
            ->count();

        // 9. HISTORIQUE DES TRANSACTIONS
        $transactions = Transaction::where('agence_id', $agence->id)
            ->whereMonth('date_transaction', $dateFiltre->month)
            ->whereYear('date_transaction', $dateFiltre->year)
            ->with(['contrat.locataire.user', 'proprietaire.user', 'user'])
            ->orderBy('date_transaction', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 10. BALANCE ÂGÉE & CRÉANCES DES LOYERS (RECOUVREMENT)
        $comptesBancaires = CompteBancaire::where('agence_id', $agence->id)->get();

        $facturesImpayees = Facture::where('agence_id', $agence->id)
            ->where('statut', '!=', 'Payée')
            ->with(['contrat.locataire.user', 'contrat.bien.familleDeBien', 'contrat.bien.proprietaire.user'])
            ->orderBy('date_echeance', 'asc')
            ->get();

        $tranchesBalanceAgee = [
            '0_15' => [
                'label' => '0 - 15 jours',
                'sous_titre' => 'Retard léger (Rappel amiable)',
                'total' => 0,
                'nb' => 0,
                'factures' => [],
                'color' => 'emerald',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icon' => 'fa-clock'
            ],
            '16_30' => [
                'label' => '16 - 30 jours',
                'sous_titre' => 'Retard modéré (Relance 1)',
                'total' => 0,
                'nb' => 0,
                'factures' => [],
                'color' => 'amber',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                'icon' => 'fa-triangle-exclamation'
            ],
            '31_60' => [
                'label' => '31 - 60 jours',
                'sous_titre' => 'Retard critique (Mise en demeure)',
                'total' => 0,
                'nb' => 0,
                'factures' => [],
                'color' => 'orange',
                'badge' => 'bg-orange-50 text-orange-700 border-orange-200',
                'icon' => 'fa-bell'
            ],
            '60_plus' => [
                'label' => '+ de 60 jours',
                'sous_titre' => 'Contentieux / Juridique',
                'total' => 0,
                'nb' => 0,
                'factures' => [],
                'color' => 'rose',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
                'icon' => 'fa-gavel'
            ],
        ];

        $creances = [];
        $totalCreances = 0;
        $locatairesDebiteurs = [];
        $now = Carbon::now();

        foreach ($facturesImpayees as $facture) {
            $resteAPayer = max(0, (float)$facture->montant_total_du - (float)$facture->montant_deja_paye);
            if ($resteAPayer <= 0) continue;

            $dateEcheance = $facture->date_echeance ? Carbon::parse($facture->date_echeance) : $facture->created_at;
            $estEnRetard = $now->gt($dateEcheance);
            $joursRetard = $estEnRetard ? (int)$dateEcheance->diffInDays($now) : 0;

            if ($joursRetard <= 15) {
                $codeTranche = '0_15';
            } elseif ($joursRetard <= 30) {
                $codeTranche = '16_30';
            } elseif ($joursRetard <= 60) {
                $codeTranche = '31_60';
            } else {
                $codeTranche = '60_plus';
            }

            // Téléphone et message WhatsApp
            $telBrut = $facture->contrat?->locataire?->user?->telephone ?? '';
            $telWhatsApp = preg_replace('/[^0-9]/', '', $telBrut);
            if (strlen($telWhatsApp) === 10 && str_starts_with($telWhatsApp, '0')) {
                $telWhatsApp = '225' . substr($telWhatsApp, 1);
            } elseif (strlen($telWhatsApp) === 8) {
                $telWhatsApp = '225' . $telWhatsApp;
            }

            $nomLocataire = $facture->contrat?->locataire?->user?->name ?? 'Cher Locataire';
            $nomBien = $facture->contrat?->bien?->nom_bien ?? 'votre logement';
            $nomAgence = $agence->nom ?? 'Notre Agence Immobilière';
            $devise = $agence->devise ?? 'XOF';
            $montantFormatte = number_format($resteAPayer, 0, ',', ' ') . ' ' . $devise;
            $dateEcheanceStr = $dateEcheance->translatedFormat('d/m/Y');

            if ($codeTranche === '0_15') {
                $msgRelance = "Bonjour {$nomLocataire}, nous espérons que vous vous portez bien. Nous vous rappelons amicalement que l'échéance de votre loyer ({$montantFormatte}) pour {$nomBien} était fixée au {$dateEcheanceStr} (Facture {$facture->numero_facture}). Merci de bien vouloir procéder à son règlement ou de nous transmettre votre preuve de virement. Bien cordialement, {$nomAgence}.";
            } elseif ($codeTranche === '16_30') {
                $msgRelance = "Bonjour {$nomLocataire}, sauf erreur de notre part, votre loyer pour {$nomBien} présente un retard de {$joursRetard} jours (Montant restant dû : {$montantFormatte}). Nous vous prions de bien vouloir régulariser votre situation dans les meilleurs délais. Restant à votre écoute, {$nomAgence}.";
            } elseif ($codeTranche === '31_60') {
                $msgRelance = "URGENT - MISE EN DEMEURE : {$nomLocataire}, nous constatons un arriéré de loyer impayé de {$montantFormatte} depuis {$joursRetard} jours pour le bien {$nomBien} (Facture {$facture->numero_facture}). Merci de procéder au paiement immédiat sous 48h afin d'éviter des poursuites. Direction {$nomAgence}.";
            } else {
                $msgRelance = "AVIS CONTENTIEUX RECOUVREMENT : {$nomLocataire}, votre dossier pour impayé de loyer de {$montantFormatte} ({$joursRetard} jours de retard) sur le bien {$nomBien} fait l'objet d'une procédure contentieuse. Veuillez contacter d'urgence notre service recouvrement pour régularisation immédiate. Service Recouvrement - {$nomAgence}.";
            }

            $item = (object)[
                'facture' => $facture,
                'reste_a_payer' => $resteAPayer,
                'jours_retard' => $joursRetard,
                'tranche' => $codeTranche,
                'tranche_info' => $tranchesBalanceAgee[$codeTranche],
                'tel_brut' => $telBrut,
                'tel_whatsapp' => $telWhatsApp,
                'whatsapp_url' => $telWhatsApp ? 'https://wa.me/' . $telWhatsApp . '?text=' . rawurlencode($msgRelance) : null,
                'message_relance' => $msgRelance,
                'date_echeance' => $dateEcheance,
            ];

            $tranchesBalanceAgee[$codeTranche]['total'] += $resteAPayer;
            $tranchesBalanceAgee[$codeTranche]['nb']++;
            $tranchesBalanceAgee[$codeTranche]['factures'][] = $item;

            $creances[] = $item;
            $totalCreances += $resteAPayer;

            if ($facture->contrat?->locataire_id) {
                $locatairesDebiteurs[$facture->contrat->locataire_id] = true;
            }
        }

        $nbCreances = count($creances);
        $nbLocatairesDebiteurs = count($locatairesDebiteurs);

        // 11. ANALYTIQUE HISTORIQUE (ÉVOLUTION MENSUELLE & RENTABILITÉ AGENCE SUR 12 MOIS)
        $analytics = $this->getHistoricalAnalytics($agence->id, $dateFiltre);

        return view('finance.index', compact(
            'cautions',
            'totalCautions',
            'totalCautionsDisponibles',
            'totalCautionsRemboursees',
            'totalCautionsRetenues',
            'proprietaires',
            'revenusAgence',
            'commissionsLoyers',
            'fraisDossier',
            'visites',
            'retenuesCaution',
            'depensesAgence',
            'soldeAgence',
            'tauxMargeAgence',
            'loyersEmis',
            'loyersEncaisses',
            'resteARecouvrer',
            'tauxRecouvrement',
            'nbFacturesTotal',
            'nbFacturesPayees',
            'nbFacturesPartielles',
            'nbFacturesImpayees',
            'totalReversementsMois',
            'totalSoldeEnAttente',
            'proprietairesEnAttenteCount',
            'paiementsModes',
            'totalPaiementsMois',
            'valeurLocativeActive',
            'contratsActifsCount',
            'transactions',
            'reversements',
            'comptesBancaires',
            'creances',
            'totalCreances',
            'nbCreances',
            'nbLocatairesDebiteurs',
            'tranchesBalanceAgee',
            'listeMois',
            'moisInputValue',
            'moisFormat',
            'analytics'
        ));
    }

    // Fonction pour rembourser une caution
    public function rembourserCaution(Request $request, Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'montant_rembourse' => 'required|numeric|min:0',
            'montant_retenu' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'date_remboursement' => 'required|date',
        ]);

        $disponible = $contrat->montant_caution - $contrat->montant_caution_rembourse - $contrat->montant_caution_retenu;
        $total = $request->montant_rembourse + $request->montant_retenu;
        if ($total > $disponible) {
            return back()->withErrors('Le montant total (remboursement + retenu) dépasse la caution disponible.');
        }

        DB::transaction(function () use ($contrat, $request) {
            $contrat->update([
                'montant_caution_rembourse' => $contrat->montant_caution_rembourse + $request->montant_rembourse,
                'montant_caution_retenu' => $contrat->montant_caution_retenu + $request->montant_retenu,
                'statut' => 'Terminé',
                'date_cloture' => $request->date_remboursement,
            ]);

            if ($contrat->bien) {
                $contrat->bien->update(['statut' => 'Disponible']);
            }

            // Créer une transaction pour le remboursement
            if ($request->montant_rembourse > 0) {
                Transaction::create([
                    'agence_id' => $contrat->agence_id,
                    'type_compte' => 'caution',
                    'contrat_id' => $contrat->id,
                    'type_transaction' => 'remboursement_caution',
                    'sens' => 'debit',
                    'montant' => $request->montant_rembourse,
                    'devise' => Auth::user()->agence->devise ?? 'XOF',
                    'reference' => 'REM-CAU-' . strtoupper(uniqid()),
                    'description' => $request->notes,
                    'date_transaction' => $request->date_remboursement,
                    'user_id' => Auth::id(),
                ]);
            }

            // Créer une transaction pour la retenue (si > 0)
            if ($request->montant_retenu > 0) {
                Transaction::create([
                    'agence_id' => $contrat->agence_id,
                    'type_compte' => 'agence',
                    'contrat_id' => $contrat->id,
                    'type_transaction' => 'retenue_caution',
                    'sens' => 'credit',
                    'montant' => $request->montant_retenu,
                    'devise' => Auth::user()->agence->devise ?? 'XOF',
                    'reference' => 'RET-CAU-' . strtoupper(uniqid()),
                    'description' => $request->notes,
                    'date_transaction' => $request->date_remboursement,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return back()->with('success', 'Remboursement enregistré avec succès.');
    }

    // Fonction pour créer un reversement
    public function creerReversement(Request $request, Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'date_reversement' => 'required|date',
            'periode' => 'nullable|string',
            'montant_total_loyers' => 'nullable|numeric',
            'commission_agence' => 'nullable|numeric',
            'montant_depenses' => 'nullable|numeric',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'compte_bancaire_id' => 'nullable|exists:comptes_bancaires,id',
        ]);

        $periode = $request->periode ?: \Carbon\Carbon::parse($request->date_reversement)->translatedFormat('F Y');

        DB::transaction(function () use ($proprietaire, $request, $periode) {
            $reversement = Reversement::create([
                'agence_id' => $proprietaire->agence_id,
                'proprietaire_id' => $proprietaire->id,
                'compte_bancaire_id' => $request->compte_bancaire_id,
                'montant_total_loyers' => $request->montant_total_loyers ?? $request->montant,
                'commission_agence' => $request->commission_agence ?? 0,
                'montant_depenses' => $request->montant_depenses ?? 0,
                'montant_net_verse' => $request->montant,
                'periode' => $periode,
                'date_reversement' => $request->date_reversement,
                'reference_transaction' => $request->reference,
                'notes' => $request->notes,
            ]);

            // Créer la transaction
            Transaction::create([
                'agence_id' => $proprietaire->agence_id,
                'type_compte' => 'proprietaire',
                'proprietaire_id' => $proprietaire->id,
                'type_transaction' => 'reversement',
                'sens' => 'debit',
                'montant' => $request->montant,
                'devise' => Auth::user()->agence->devise ?? 'XOF',
                'reference' => $request->reference ?? 'REV-' . strtoupper(uniqid()),
                'description' => ($request->notes ? $request->notes . ' • ' : '') . 'Reversement propriétaire (' . $periode . ')',
                'date_transaction' => $request->date_reversement,
                'reversement_id' => $reversement->id,
                'user_id' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Reversement de ' . number_format($request->montant, 0, ',', ' ') . ' ' . (Auth::user()->agence->devise ?? 'XOF') . ' enregistré avec succès pour la période ' . $periode . '.');
    }

    // Fonction pour annuler / supprimer un reversement
    public function supprimerReversement(Reversement $reversement)
    {
        if ($reversement->agence_id !== Auth::user()->agence_id) abort(403);

        DB::transaction(function () use ($reversement) {
            Transaction::where('reversement_id', $reversement->id)->delete();
            $reversement->delete();
        });

        return back()->with('success', 'Reversement annulé avec succès.');
    }

    // Fonction pour effectuer des reversements en masse (Batch Payout)
    public function creerReversementsBatch(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'reversements' => 'required|array|min:1',
            'reversements.*.proprietaire_id' => 'required|exists:proprietaires,id',
            'reversements.*.montant' => 'required|numeric|min:0.01',
            'reversements.*.loyers' => 'nullable|numeric|min:0',
            'reversements.*.commissions' => 'nullable|numeric|min:0',
            'reversements.*.depenses' => 'nullable|numeric|min:0',
            'date_reversement' => 'required|date',
            'periode' => 'nullable|string',
            'compte_bancaire_id' => 'nullable|exists:comptes_bancaires,id',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $dateReversement = $request->date_reversement;
        $periode = $request->periode ?: Carbon::parse($dateReversement)->translatedFormat('F Y');
        $compteBancaireId = $request->compte_bancaire_id;
        $globalRef = $request->reference ? trim($request->reference) : 'BATCH-REV-' . date('Ymd-His');
        $globalNotes = $request->notes;

        $nbReussis = 0;
        $totalVerse = 0;

        DB::transaction(function () use ($request, $agence, $periode, $dateReversement, $compteBancaireId, $globalRef, $globalNotes, &$nbReussis, &$totalVerse) {
            foreach ($request->reversements as $item) {
                $proprietaire = Proprietaire::where('id', $item['proprietaire_id'])
                    ->where('agence_id', $agence->id)
                    ->first();

                if (!$proprietaire) continue;

                $montant = (float) $item['montant'];
                if ($montant <= 0) continue;

                $loyers = isset($item['loyers']) ? (float)$item['loyers'] : $montant;
                $commissions = isset($item['commissions']) ? (float)$item['commissions'] : 0;
                $depenses = isset($item['depenses']) ? (float)$item['depenses'] : 0;

                $refIndiv = $globalRef . '-P' . $proprietaire->id;

                $rev = Reversement::create([
                    'agence_id' => $agence->id,
                    'proprietaire_id' => $proprietaire->id,
                    'compte_bancaire_id' => $compteBancaireId,
                    'montant_total_loyers' => $loyers,
                    'commission_agence' => $commissions,
                    'montant_depenses' => $depenses,
                    'montant_net_verse' => $montant,
                    'periode' => $periode,
                    'date_reversement' => $dateReversement,
                    'reference_transaction' => $refIndiv,
                    'notes' => ($globalNotes ? $globalNotes . ' • ' : '') . 'Reversement groupé',
                ]);

                Transaction::create([
                    'agence_id' => $agence->id,
                    'type_compte' => 'proprietaire',
                    'proprietaire_id' => $proprietaire->id,
                    'type_transaction' => 'reversement',
                    'sens' => 'debit',
                    'montant' => $montant,
                    'devise' => $agence->devise ?? 'XOF',
                    'reference' => $refIndiv,
                    'description' => ($globalNotes ? $globalNotes . ' • ' : '') . 'Reversement groupé (' . $periode . ')',
                    'date_transaction' => $dateReversement,
                    'reversement_id' => $rev->id,
                    'user_id' => Auth::id(),
                ]);

                $nbReussis++;
                $totalVerse += $montant;
            }
        });

        $devise = $agence->devise ?? 'XOF';
        $msg = "Reversement groupé réussi : {$nbReussis} propriétaire(s) réglé(s) pour un montant total de " . number_format($totalVerse, 0, ',', ' ') . " {$devise} (Période {$periode}).";

        return back()->with('success', $msg);
    }

    /**
     * Analyse historique des flux locatifs et de la rentabilité de l'agence (12 derniers mois jusqu'à la date sélectionnée)
     */
    private function getHistoricalAnalytics(int $agenceId, Carbon $dateFiltre): array
    {
        $labels = [];
        $loyersEmis = [];
        $loyersEncaisses = [];
        $reversements = [];
        $revenusAgence = [];
        $depensesAgence = [];
        $margeNette = [];

        $totaux = [
            'loyers_emis' => 0,
            'loyers_encaisses' => 0,
            'reversements' => 0,
            'taux_recouvrement_global' => 0,
            'revenus_agence' => 0,
            'depenses_agence' => 0,
            'marge_nette' => 0,
            'taux_marge_global' => 0,
        ];

        for ($i = 11; $i >= 0; $i--) {
            $targetDate = $dateFiltre->copy()->subMonths($i);
            $moisLabel = ucfirst($targetDate->translatedFormat('M y'));
            $moisFormat = $targetDate->translatedFormat('F Y');

            // 1. Factures du mois (Loyer + Initial)
            $facturesMois = Facture::where('agence_id', $agenceId)
                ->whereRaw('LOWER(mois_facture) = ?', [mb_strtolower($moisFormat)])
                ->get();

            $emis = 0;
            $encaisses = 0;
            foreach ($facturesMois as $f) {
                if ($f->type_facture === 'Loyer') {
                    $emis += (float)$f->montant_total_du;
                    $encaisses += (float)$f->montant_deja_paye;
                } elseif ($f->type_facture === 'Initial') {
                    if ($f->montant_total_du > 0 && $f->montant_loyer_initial > 0) {
                        $prop = $f->montant_loyer_initial / $f->montant_total_du;
                        $emis += (float)$f->montant_loyer_initial;
                        $encaisses += (float)($f->montant_deja_paye * $prop);
                    }
                }
            }

            // 2. Reversements propriétaires effectués
            $revers = (float) Reversement::where('agence_id', $agenceId)
                ->whereMonth('date_reversement', $targetDate->month)
                ->whereYear('date_reversement', $targetDate->year)
                ->sum('montant_net_verse');

            // 3. Commissions loyers classiques
            $commLoyersClassique = (float) Paiement::where('paiements.agence_id', $agenceId)
                ->join('factures', 'paiements.facture_id', '=', 'factures.id')
                ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
                ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                ->whereRaw('LOWER(factures.mois_facture) = ?', [mb_strtolower($moisFormat)])
                ->where('factures.type_facture', 'Loyer')
                ->selectRaw('SUM(paiements.montant_verse * (biens.taux_commission_agence / 100)) as comm')
                ->value('comm') ?? 0;

            // 4. Commissions et frais sur factures initiales
            $facturesInitComms = Paiement::where('paiements.agence_id', $agenceId)
                ->join('factures', 'paiements.facture_id', '=', 'factures.id')
                ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
                ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                ->whereRaw('LOWER(factures.mois_facture) = ?', [mb_strtolower($moisFormat)])
                ->where('factures.type_facture', 'Initial')
                ->select('factures.montant_total_du', 'factures.montant_loyer_initial', 'factures.frais_dossier', 'paiements.montant_verse', 'biens.taux_commission_agence')
                ->get();

            $commLoyerInitial = 0;
            $fraisDossierInit = 0;
            foreach ($facturesInitComms as $pInit) {
                if ($pInit->montant_total_du > 0) {
                    if ($pInit->montant_loyer_initial > 0) {
                        $propLoyer = $pInit->montant_loyer_initial / $pInit->montant_total_du;
                        $loyerPaye = $pInit->montant_verse * $propLoyer;
                        $commLoyerInitial += $loyerPaye * ($pInit->taux_commission_agence / 100);
                    }
                    if ($pInit->frais_dossier > 0) {
                        $propFrais = $pInit->frais_dossier / $pInit->montant_total_du;
                        $fraisDossierInit += $pInit->montant_verse * $propFrais;
                    }
                }
            }

            // 5. Frais de dossier facturés séparément
            $fraisDossierSepares = (float) Paiement::where('paiements.agence_id', $agenceId)
                ->join('factures', 'paiements.facture_id', '=', 'factures.id')
                ->whereRaw('LOWER(factures.mois_facture) = ?', [mb_strtolower($moisFormat)])
                ->where('factures.type_facture', 'Frais Dossier')
                ->sum('paiements.montant_verse');

            // 6. Visites
            $visites = (float) \App\Models\Visite::where('agence_id', $agenceId)
                ->whereMonth('created_at', $targetDate->month)
                ->whereYear('created_at', $targetDate->year)
                ->sum('montant_paye');

            // 7. Retenues cautions
            $retenuesCaution = (float) Transaction::where('agence_id', $agenceId)
                ->where('type_transaction', 'retenue_caution')
                ->whereMonth('date_transaction', $targetDate->month)
                ->whereYear('date_transaction', $targetDate->year)
                ->sum('montant');

            $revAgence = round($commLoyersClassique + $commLoyerInitial + $fraisDossierSepares + $fraisDossierInit + $visites + $retenuesCaution);

            // 8. Dépenses d'exploitation de l'agence
            $depAgence = (float) Depense::where('agence_id', $agenceId)
                ->whereMonth('date_depense', $targetDate->month)
                ->whereYear('date_depense', $targetDate->year)
                ->sum('montant');

            $marge = $revAgence - $depAgence;

            $labels[] = $moisLabel;
            $loyersEmis[] = round($emis);
            $loyersEncaisses[] = round($encaisses);
            $reversements[] = round($revers);
            $revenusAgence[] = round($revAgence);
            $depensesAgence[] = round($depAgence);
            $margeNette[] = round($marge);

            $totaux['loyers_emis'] += round($emis);
            $totaux['loyers_encaisses'] += round($encaisses);
            $totaux['reversements'] += round($revers);
            $totaux['revenus_agence'] += round($revAgence);
            $totaux['depenses_agence'] += round($depAgence);
            $totaux['marge_nette'] += round($marge);
        }

        if ($totaux['loyers_emis'] > 0) {
            $totaux['taux_recouvrement_global'] = round(($totaux['loyers_encaisses'] / $totaux['loyers_emis']) * 100, 1);
        } else {
            $totaux['taux_recouvrement_global'] = 100;
        }

        if ($totaux['revenus_agence'] > 0) {
            $totaux['taux_marge_global'] = round(($totaux['marge_nette'] / $totaux['revenus_agence']) * 100, 1);
        } else {
            $totaux['taux_marge_global'] = 0;
        }

        return [
            'labels' => $labels,
            'loyers_emis' => $loyersEmis,
            'loyers_encaisses' => $loyersEncaisses,
            'reversements' => $reversements,
            'revenus_agence' => $revenusAgence,
            'depenses_agence' => $depensesAgence,
            'marge_nette' => $margeNette,
            'totaux' => $totaux,
        ];
    }
}
