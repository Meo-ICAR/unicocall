<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'trainable_type',
        'trainable_id',
        'regulatory_framework',
        'course_title',
        'course_description',
        'provider',
        'trainer',
        'delivery_mode',
        'training_date',
        'expiry_date',
        'hours',
        'outcome',
        'score',
        'certificate_issued',
        'certificate_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'training_date'       => 'date',
            'expiry_date'         => 'date',
            'hours'               => 'decimal:1',
            'score'               => 'decimal:2',
            'certificate_issued'  => 'boolean',
            'created_at'          => 'datetime',
            'updated_at'          => 'datetime',
            'deleted_at'          => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function trainable(): MorphTo
    {
        return $this->morphTo();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByFramework($query, string $framework)
    {
        return $query->where('regulatory_framework', $framework);
    }

    public function scopeForEmployees($query)
    {
        return $query->where('trainable_type', Employee::class);
    }

    public function scopeForClients($query)
    {
        return $query->where('trainable_type', Client::class);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    public function scopeValid($query)
    {
        return $query->where(fn($q) =>
            $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now())
        );
    }

    public function scopePassed($query)
    {
        return $query->whereIn('outcome', ['passed', 'attended']);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date
            && !$this->is_expired
            && $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) return null;
        return (int) now()->diffInDays($this->expiry_date, false);
    }

    public function getExpiryStatusColorAttribute(): string
    {
        if (!$this->expiry_date) return 'gray';
        return match (true) {
            $this->is_expired        => 'danger',
            $this->is_expiring_soon  => 'warning',
            default                  => 'success',
        };
    }

    public function getRegulatoryFrameworkLabelAttribute(): string
    {
        return self::getRegulatoryFrameworkOptions()[$this->regulatory_framework] ?? $this->regulatory_framework;
    }

    public function getOutcomeLabelAttribute(): string
    {
        return self::getOutcomeOptions()[$this->outcome] ?? $this->outcome;
    }

    public function getOutcomeColorAttribute(): string
    {
        return match ($this->outcome) {
            'passed'   => 'success',
            'attended' => 'info',
            'partial'  => 'warning',
            'failed'   => 'danger',
            default    => 'gray',
        };
    }

    public function getTrainableNameAttribute(): string
    {
        return $this->trainable?->name ?? '—';
    }

    public function getTrainableTypeLabel(): string
    {
        return match ($this->trainable_type) {
            Employee::class => 'Dipendente',
            Client::class   => 'Cliente/Consulente',
            default         => $this->trainable_type,
        };
    }

    // ── Options ──────────────────────────────────────────────────────────────

    public static function getRegulatoryFrameworkOptions(): array
    {
        return [
            'gdpr'              => 'GDPR — Reg. UE 2016/679',
            'oam'               => 'OAM — Mediatori Creditizi',
            'ivass'             => 'IVASS — Intermediari Assicurativi',
            'sicurezza_lavoro'  => 'Sicurezza sul Lavoro — D.Lgs. 81/08',
            'antiriciclaggio'   => 'Antiriciclaggio — D.Lgs. 231/07',
            'mifid'             => 'MiFID II — Servizi di Investimento',
            'other'             => 'Altro',
        ];
    }

    public static function getDeliveryModeOptions(): array
    {
        return [
            'in_person'  => 'In aula',
            'online'     => 'E-learning / Online',
            'blended'    => 'Blended (misto)',
            'on_the_job' => 'Affiancamento (OJT)',
            'webinar'    => 'Webinar',
        ];
    }

    public static function getOutcomeOptions(): array
    {
        return [
            'passed'   => 'Superato',
            'attended' => 'Frequentato',
            'partial'  => 'Parziale',
            'failed'   => 'Non superato',
        ];
    }

    public static function getTrainableTypeOptions(): array
    {
        return [
            Employee::class => 'Dipendente',
            Client::class   => 'Cliente / Consulente',
        ];
    }

    // ── booted ───────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $record) {
            if (empty($record->company_id) && auth()->check()) {
                if (function_exists('filament') && filament()->getTenant()) {
                    $record->company_id = filament()->getTenant()->id;
                } elseif (auth()->user()->companies()->exists()) {
                    $record->company_id = auth()->user()->companies()->first()->id;
                }
            }
        });
    }
}
