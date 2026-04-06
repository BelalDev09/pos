<?php

namespace App\Repositories;

use App\Models\Inventory;
class OrderRepository
{
    public function create(array $data)
    {
        return Inventory::create($data);
    }

    public function update(Inventory $inventory, array $data)
    {
        $inventory->update($data);
        return $inventory;
    }

    public function delete(Inventory $inventory)
    {
        return $inventory->delete();
    }
}