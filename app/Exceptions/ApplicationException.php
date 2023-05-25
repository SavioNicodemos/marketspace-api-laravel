<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApplicationException extends Exception
{
    protected $message;

    protected $code;

    public function __construct($message, $code = 400)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json(['message' => ucfirst($this->message)], $this->code);
    }
}
