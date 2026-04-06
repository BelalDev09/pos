<?php

// File: app/Exceptions/InsufficientStockException.php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $message = 'Insufficient stock.', int $code = 422)
    {
        parent::__construct($message, $code);
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error'   => 'insufficient_stock',
        ], 422);
    }
}
