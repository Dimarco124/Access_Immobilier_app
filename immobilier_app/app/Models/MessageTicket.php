<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTicket extends Model
{
    use HasFactory;

    protected $table = 'messages_ticket';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'contenu_message',
        'piece_jointe_url',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
