<?php

namespace App\Traits;

use App\Models\StockMovement;

trait HasStockMovements
{
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
