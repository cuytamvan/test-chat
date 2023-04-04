<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function render() {
        return response()->json([
            'message' => $this->getMessage(),
        ], 400);
    }
}
