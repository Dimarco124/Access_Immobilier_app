<?php

namespace App\Notifications;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaiementRecuNotification extends Notification
{
    use Queueable;

    protected $paiement;

    public function __construct(Paiement $paiement)
    {
        $this->paiement = $paiement;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        $facture = $this->paiement->facture;
        $devise = $notifiable->agence->devise ?? 'CDF';

        return [
            'paiement_id' => $this->paiement->id,
            'facture_id' => $facture->id,
            'montant' => $this->paiement->montant_verse,
            'devise' => $devise,
            'message' => "Paiement de " . number_format($this->paiement->montant_verse, 0, ',', ' ') . " {$devise} reçu pour la facture #{$facture->numero_facture}.",
            'url' => route('factures.index'),
            'type' => 'paiement_recu',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $facture = $this->paiement->facture;
        $devise = $notifiable->agence->devise ?? 'CDF';
        $montant = number_format($this->paiement->montant_verse, 0, ',', ' ');

        return (new MailMessage)
            ->subject("Paiement reçu - Facture #{$facture->numero_facture}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Nous avons bien reçu votre paiement de {$montant} {$devise}.")
            ->line("**Facture:** #{$facture->numero_facture}")
            ->line("**Mode de paiement:** {$this->paiement->mode_paiement}")
            ->line("**Date:** {$this->paiement->date_paiement->format('d/m/Y')}")
            ->action('Voir mes factures', route('factures.index'))
            ->line('Merci pour votre confiance.');
    }
}
