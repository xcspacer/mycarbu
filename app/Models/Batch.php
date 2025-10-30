<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'station',
        'goa_quantity',
        'goa_used',
        'goa_discount_per_liter',
        'goa_plus_discount_per_liter',
        'sp95_quantity',
        'sp95_used',
        'sp95_discount_per_liter',
        'sp95_plus_discount_per_liter',
        'sp98_quantity',
        'sp98_used',
        'sp98_discount_per_liter',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'goa_quantity' => 'integer',
        'goa_used' => 'integer',
        'goa_discount_per_liter' => 'decimal:5',
        'goa_plus_discount_per_liter' => 'decimal:5',
        'sp95_quantity' => 'integer',
        'sp95_used' => 'integer',
        'sp95_discount_per_liter' => 'decimal:5',
        'sp95_plus_discount_per_liter' => 'decimal:5',
        'sp98_quantity' => 'integer',
        'sp98_used' => 'integer',
        'sp98_discount_per_liter' => 'decimal:5',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now()->startOfDay());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForStation($query, $station)
    {
        return $query->where('station', $station);
    }

    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->where('goa_quantity', '>', 'goa_used')
              ->orWhere('sp95_quantity', '>', 'sp95_used')
              ->orWhere('sp98_quantity', '>', 'sp98_used');
        });
    }

    public function getStationTextAttribute(): string
    {
        return ucfirst($this->station);
    }

    public function isActive(): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now->startOfDay();
    }

    public function hasAvailableQuantity(): bool
    {
        return $this->goa_quantity > $this->goa_used || 
               $this->sp95_quantity > $this->sp95_used || 
               $this->sp98_quantity > $this->sp98_used;
    }

    public function getTotalQuantityAttribute(): float
    {
        return $this->goa_quantity + $this->sp95_quantity + $this->sp98_quantity;
    }

    public function getTotalUsedAttribute(): float
    {
        return $this->goa_used + $this->sp95_used + $this->sp98_used;
    }

    public function getTotalRemainingAttribute(): float
    {
        return $this->total_quantity - $this->total_used;
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->total_quantity == 0) {
            return 0;
        }
        return ($this->total_used / $this->total_quantity) * 100;
    }

    public function getGoaRemainingAttribute(): float
    {
        return $this->goa_quantity - $this->goa_used;
    }

    public function getSp95RemainingAttribute(): float
    {
        return $this->sp95_quantity - $this->sp95_used;
    }

    public function getSp98RemainingAttribute(): float
    {
        return $this->sp98_quantity - $this->sp98_used;
    }

    public function canUseFuel(string $fuelType, float $quantity): bool
    {
        $remaining = $this->getRemainingQuantity($fuelType);
        return $remaining >= $quantity;
    }

    public function getRemainingQuantity(string $fuelType): float
    {
        return match($fuelType) {
            'goa' => $this->goa_remaining,
            'sp95' => $this->sp95_remaining,
            'sp98' => $this->sp98_remaining,
            default => 0
        };
    }

    public function getDiscountPerLiter(string $fuelType): float
    {
        return match($fuelType) {
            'goa' => $this->goa_discount_per_liter,
            'goaaditivo' => $this->goa_plus_discount_per_liter,
            'sp95' => $this->sp95_discount_per_liter,
            'sp95aditivo' => $this->sp95_plus_discount_per_liter,
            'sp98' => $this->sp98_discount_per_liter,
            default => 0
        };
    }

    public static function getNormalizedFuelType(string $fuelType): string
    {
        return match($fuelType) {
            'goa', 'goaaditivo' => 'goa',
            'sp95', 'sp95aditivo' => 'sp95',
            'sp98' => 'sp98',
            default => $fuelType
        };
    }

    public function getFuelTypesWithQuantities(): array
    {
        $types = [];
        
        if ($this->goa_quantity > 0) {
            $types[] = [
                'type' => 'goa',
                'label' => 'GOA',
                'quantity' => $this->goa_quantity,
                'used' => $this->goa_used,
                'remaining' => $this->goa_remaining
            ];
        }
        
        if ($this->sp95_quantity > 0) {
            $types[] = [
                'type' => 'sp95',
                'label' => 'SP95',
                'quantity' => $this->sp95_quantity,
                'used' => $this->sp95_used,
                'remaining' => $this->sp95_remaining
            ];
        }
        
        if ($this->sp98_quantity > 0) {
            $types[] = [
                'type' => 'sp98',
                'label' => 'SP98',
                'quantity' => $this->sp98_quantity,
                'used' => $this->sp98_used,
                'remaining' => $this->sp98_remaining
            ];
        }
        
        return $types;
    }
}
