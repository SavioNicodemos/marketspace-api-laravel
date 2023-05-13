<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Response;

class ViewImageController extends Controller
{
    use ApiResponser;
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $imageName)
    {
        $path = public_path().'/storage/avatars/'.$imageName;
        return Response::download($path);
    }
}
