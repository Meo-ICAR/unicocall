<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataSubjectRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'client_id',
        'requester_name',
        'requester_email',
        'requester_phone',
        'request_type',
        'status',
        'received_at',
        'deadline_at',
        'extended_until',
        'completed_at',
        'request_description',
        'response_notes',
        'rejection_reason',
        'identity_verified',
        'identity_verification_method',
        'channel',
    ];

    protected function casts(): array
    {
        return [
            'received_at'    => 'date',
            'deadline_at'    => 'date',
            'extended_until' => 'date',
            'completed_at'   => 'date',
            'identity_verified' => 'boolean',
            'created_at'     => 'datetime',
            'updated_at'     => 'datetime',
            'deleted_at'     => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('request_type', $type);
    }

    public function scopeOverdue($query)
    {
        return $query
            ->whereNotIn('status', ['completed', 'rejected'])
            ->where(function ($q) {
                $q->where(fn($q2) => $q2->whereNull('extended_until')->where('deadline_at', '<', now()))
                  ->orWhere('extended_until', '<', now());
            });
    }

    public function scopeDueSoon($query, int $days = 5)
    {
        return $query
            ->whereNotIn('status', ['completed', 'rejected'])
            ->where(function ($q) use ($days) {
                $q->where(fn($q2) => $q2->whereNull('extended_until')->whereBetween('deadline_at', [now(), now()->addDays($days)]))
                  ->orWhereBetween('extended_until', [now(), now()->addDays($days)]);
            });
    }

    public function scopePending($query)
    {
        return $query->whereNotIn('status', ['completed', 'rejected']);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getEffectiveDeadlineAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->extended_until ?? $this->deadline_at;
    }

    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['completed', 'rejected'])) {
            return false;
        }
        return $this->effective_deadline?->isPast() ?? false;
    }

    public function getDaysRemainingAttribute(): int
    {
        if (in_array($this->status, ['completed', 'rejected'])) {
            return 0;
        }
        return (int) now()->diffInDays($this->effective_deadline, false);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'received'    => 'info',
            'in_progress' => 'warning',
            'completed'   => 'success',
            'rejected'    => 'danger',
            'extended'    => 'gray',
            default       => 'gray',
        };
    }

    public function getRequestTypeLabelAttribute(): string
    {
        return self::getRequestTypeOptions()[$this->request_type] ?? $this->request_type;
    }

    public function getChannelLabelAttribute(): string
    {
        return self::getChannelOptions()[$this->channel] ?? $this->channel;
    }

    // ── Methods ──────────────────────────────────────────────────────────────

    public function markInProgress(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    public function markCompleted(string $notes = null): void
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'response_notes' => $notes ?? $this->response_notes,
        ]);
    }

    public function markRejected(string $reason): void
    {
        $this->update([
            'status'           => 'rejected',
            'completed_at'     => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function extendDeadline(): void
    {
        $this->update([
            'status'         => 'extended',
            'extended_until' => $this->received_at->addDays(90), // +2 mesi ≈ 90 gg
        ]);
    }

    // ── Constants & Options ──────────────────────────────────────────────────

    public static function getRequestTypeOptions(): array
    {
        return [
            'access'           => 'Art. 15 — Diritto di accesso',
            'rectification'    => 'Art. 16 — Rettifica',
            'erasure'          => 'Art. 17 — Cancellazione (diritto all\'oblio)',
            'restriction'      => 'Art. 18 — Limitazione del trattamento',
            'portability'      => 'Art. 20 — Portabilità dei dati',
            'objection'        => 'Art. 21 — Opposizione al trattamento',
            'withdraw_consent' => 'Art. 7 par. 3 — Revoca del consenso',
            'other'            => 'Altra richiesta',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'received'    => 'Ricevuta',
            'in_progress' => 'In lavorazione',
            'completed'   => 'Evasa',
            'rejected'    => 'Rifiutata',
            'extended'    => 'Prorogata',
        ];
    }

    public static function getChannelOptions(): array
    {
        return [
            'email'       => 'Email',
            'pec'         => 'PEC',
            'letter'      => 'Lettera cartacea',
            'in_person'   => 'Di persona',
            'online_form' => 'Modulo online',
            'other'       => 'Altro',
        ];
    }

    public static function getVerificationMethodOptions(): array
    {
        return [
            'CIE'        => 'Carta d\'Identità Elettronica',
            'SPID'       => 'SPID',
            'passport'   => 'Passaporto',
            'email'      => 'Verifica via email',
            'in_person'  => 'Verifica di persona',
            'other'      => 'Altro',
        ];
    }

    // ── booted ───────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $record) {
            // Auto-imposta scadenza a 30 gg dalla ricezione (Art. 12 par. 3 GDPR)
            if (empty($record->deadline_at) && !empty($record->received_at)) {
                $record->deadline_at = $record->received_at->addDays(30);
            }

            // Auto-assegna company dal tenant Filament
            if (empty($record->company_id) && auth()->check()) {
                $user = auth()->user();
                if (function_exists('filament') && filament()->getTenant()) {
                    $record->company_id = filament()->getTenant()->id;
                } elseif ($user->companies()->exists()) {
                    $record->company_id = $user->companies()->first()->id;
                }
            }
        });
    }
}
