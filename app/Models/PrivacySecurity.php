<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivacySecurity extends Model
{
    use SoftDeletes;

    protected $table = 'privacy_security';

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'risk_level',
        'owner',
        'last_reviewed_at',
        'next_review_due',
    ];

    protected $casts = [
        'last_reviewed_at' => 'datetime',
        'next_review_due' => 'datetime',
    ];

    // Types
    const TYPE_TECHNICAL = 'technical';
    const TYPE_ORGANIZATIONAL = 'organizational';
    // Statuses
    const STATUS_PLANNED = 'planned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_IMPLEMENTED = 'implemented';
    const STATUS_DEPRECATED = 'deprecated';
    // Risk Levels
    const RISK_LOW = 'low';
    const RISK_MEDIUM = 'medium';
    const RISK_HIGH = 'high';
    const RISK_CRITICAL = 'critical';

    /**
     * Get the available types as an array.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_TECHNICAL => 'Tecnico',
            self::TYPE_ORGANIZATIONAL => 'Organizzativo',
        ];
    }

    /**
     * Get the available statuses as an array.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PLANNED => 'Pianificato',
            self::STATUS_IN_PROGRESS => 'In Corso',
            self::STATUS_IMPLEMENTED => 'Implementato',
            self::STATUS_DEPRECATED => 'Deprecato',
        ];
    }

    /**
     * Get the available risk levels as an array.
     */
    public static function getRiskLevels(): array
    {
        return [
            self::RISK_LOW => 'Basso',
            self::RISK_MEDIUM => 'Medio',
            self::RISK_HIGH => 'Alto',
            self::RISK_CRITICAL => 'Critico',
        ];
    }

    /**
     * Get the type label.
     */
    public function getTypeLabel(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Get the status label.
     */
    public function getStatusLabel(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get the risk level label.
     */
    public function getRiskLevelLabel(): string
    {
        return self::getRiskLevels()[$this->risk_level] ?? $this->risk_level;
    }

    /**
     * Get the risk level color for UI display.
     */
    public function getRiskLevelColor(): string
    {
        return match ($this->risk_level) {
            self::RISK_LOW => 'success',
            self::RISK_MEDIUM => 'warning',
            self::RISK_HIGH => 'danger',
            self::RISK_CRITICAL => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get the status color for UI display.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PLANNED => 'gray',
            self::STATUS_IN_PROGRESS => 'warning',
            self::STATUS_IMPLEMENTED => 'success',
            self::STATUS_DEPRECATED => 'danger',
            default => 'gray',
        };
    }

    /**
     * Check if the security measure is due for review.
     */
    public function isReviewDue(): bool
    {
        return $this->next_review_due && $this->next_review_due->isPast();
    }

    /**
     * Check if the security measure is implemented.
     */
    public function isImplemented(): bool
    {
        return $this->status === self::STATUS_IMPLEMENTED;
    }

    /**
     * Scope to get only implemented measures.
     */
    public function scopeImplemented($query)
    {
        return $query->where('status', self::STATUS_IMPLEMENTED);
    }

    /**
     * Scope to get measures by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get measures by risk level.
     */
    public function scopeByRiskLevel($query, string $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    /**
     * Scope to get measures due for review.
     */
    public function scopeReviewDue($query)
    {
        return $query
            ->whereNotNull('next_review_due')
            ->where('next_review_due', '<=', now());
    }
}
