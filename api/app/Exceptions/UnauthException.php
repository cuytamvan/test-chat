<?php

namespace App\Exceptions;

use Exception;

class UnauthException extends Exception
{
    public function render() {
        return response()->json([
            'message' => 'unauthenticated',
        ], 401);
    }
}
