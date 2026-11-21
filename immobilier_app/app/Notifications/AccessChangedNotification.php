<?php

namespace App\Notifications;

use App\Models\TeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessChangedNotification extends Notification
{
    use Queueable;

    protected $teamMember;

    public function __construct(TeamMember $teamMember)
    {
        $this->teamMember = $teamMember;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Vos accès ont été modifiés')
                    ->greeting('Bonjour ' . $notifiable->name . ' !')
                    ->line('Vos accès sur la plateforme ont été modifiés par un administrateur.')
                    ->line('Modules actuels : ' . $this->teamMember->modules_list)
                    ->line('Statut : ' . match($this->teamMember->status) {
                        'active' => '✅ Actif',
                        'suspended' => '⏸️ Suspendu',
                        'revoked' => '❌ Révoqué',
                        default => 'En attente',
                    })
                    ->action('Voir mon dashboard', route('dashboard'))
                    ->line('Pour toute question, contactez votre administrateur.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Vos accès ont été modifiés',
            'team_member_id' => $this->teamMember->id,
            'status' => $this->teamMember->status,
        ];
    }
}
