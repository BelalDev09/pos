<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory, BelongsToTenant;

    // Immutable — no updates or deletes
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'store_id',
        'tenant_id',
        'created_by',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'unit_cost',
        'reference_type',
        'reference_id',
        'reference_number',
        'batch_number',
        'expiry_date',
        'notes',
        'moved_at',
        'created_at',
    ];

    protected $casts = [
        'quantity'        => 'decimal:4',
        'quantity_before' => 'decimal:4',
        'quantity_after'  => 'decimal:4',
        'unit_cost'       => 'decimal:4',
        'expiry_date'     => 'date',
        'moved_at'        => 'datetime',
        'created_at'      => 'datetime',
    ];

    // ── Relationships 

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes 

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBetweenDates($query, string $from, string $to)
    {
        return $query->whereBetween('moved_at', [$from, $to]);
    }
}
