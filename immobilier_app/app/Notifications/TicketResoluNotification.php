<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResoluNotification extends Notification
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
            'message' => "Le ticket d'intervention #{$this->ticket->id} - {$this->ticket->objet} a été résolu.",
            'url' => route('tickets.show', $this->ticket),
            'type' => 'ticket_resolu',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket résolu #{$this->ticket->id}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre ticket d'intervention a été résolu.")
            ->line("**Objet:** {$this->ticket->objet}")
            ->line("**Bien concerné:** {$this->ticket->bien->nom_bien ?? 'N/A'}")
            ->action('Voir le ticket', route('tickets.show', $this->ticket))
            ->line('Merci de nous avoir fait confiance.');
    }
}
