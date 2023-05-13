<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class NotAuthorizedException extends Exception
{
    protected string $modelName;

    public function __construct($modelName)
    {
        $this->modelName = $modelName ? $modelName : 'Data';
    }
    /**
     * Render the exception into an HTTP response.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json(['message' => "You're not authorized to delete this ".strtolower($this->modelName.'.')], 403);
    }
}
