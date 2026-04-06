<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'account_name', 'type', 'balance'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
