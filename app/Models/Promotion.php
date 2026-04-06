<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'name', 'type', 'value', 'description', 'starts_at', 'ends_at', 'is_active'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
