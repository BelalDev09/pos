<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POSSession extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'cashier_id', 'opening_cash', 'closing_cash', 'opened_at', 'closed_at', 'status'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
