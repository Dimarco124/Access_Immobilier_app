<?php

namespace App\Notifications;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FactureGenereeNotification extends Notification
{
    use Queueable;

    protected $facture;

    public function __construct(Facture $facture)
    {
        $this->facture = $facture;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        $devise = $notifiable->agence->devise ?? 'CDF';
        $montantDu = $this->facture->montant_total_du - $this->facture->montant_deja_paye;

        return [
            'facture_id' => $this->facture->id,
            'numero_facture' => $this->facture->numero_facture,
            'montant_du' => $montantDu,
            'devise' => $devise,
            'date_echeance' => $this->facture->date_echeance,
            'message' => "Nouvelle facture #{$this->facture->numero_facture} - Montant à payer: " . number_format($montantDu, 0, ',', ' ') . " {$devise}. Échéance: {$this->facture->date_echeance->format('d/m/Y')}.",
            'url' => route('factures.index'),
            'type' => 'facture_generee',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $devise = $notifiable->agence->devise ?? 'CDF';
        $montantDu = $this->facture->montant_total_du - $this->facture->montant_deja_paye;
        $montant = number_format($montantDu, 0, ',', ' ');

        return (new MailMessage)
            ->subject("Nouvelle facture #{$this->facture->numero_facture}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Une nouvelle facture a été générée pour votre contrat.")
            ->line("**Facture:** #{$this->facture->numero_facture}")
            ->line("**Montant à payer:** {$montant} {$devise}")
            ->line("**Date d'échéance:** {$this->facture->date_echeance->format('d/m/Y')}")
            ->action('Voir la facture', route('factures.index'))
            ->line('Merci de procéder au paiement avant la date d\'échéance.');
    }
}
