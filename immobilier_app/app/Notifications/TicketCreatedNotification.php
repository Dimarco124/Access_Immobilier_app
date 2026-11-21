<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'objet' => $this->ticket->objet,
            'priorite' => $this->ticket->priorite,
            'bien' => $this->ticket->bien->nom_bien ?? 'N/A',
            'message' => "Nouveau ticket d'intervention #{$this->ticket->id} - {$this->ticket->objet} (Priorité: {$this->ticket->priorite}).",
            'url' => route('tickets.show', $this->ticket),
            'type' => 'ticket_created',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nouveau ticket d'intervention #{$this->ticket->id}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Un nouveau ticket d'intervention a été créé.")
            ->line("**Objet:** {$this->ticket->objet}")
            ->line("**Priorité:** {$this->ticket->priorite}")
            ->line("**Bien concerné:** {$this->ticket->bien->nom_bien ?? 'N/A'}")
            ->line("**Description:** " . \Illuminate\Support\Str::limit($this->ticket->description_probleme, 100))
            ->action('Voir le ticket', route('tickets.show', $this->ticket))
            ->line('Nous vous tiendrons informé de l\'évolution de votre demande.');
    }
}
