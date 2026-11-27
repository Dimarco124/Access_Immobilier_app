<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'agence_id',
        'action',
        'description',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public static function record(string $action, string $description, ?int $agenceId = null, array $metadata = [], ?int $userId = null): void
    {
        try {
            static::create([
                'user_id' => $userId ?? auth()->id(),
                'agence_id' => $agenceId ?? auth()->user()?->agence_id,
                'action' => $action,
                'description' => $description,
                'ip_address' => request()?->ip(),
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            // Ne jamais bloquer une action métier si le log échoue
        }
    }
}
