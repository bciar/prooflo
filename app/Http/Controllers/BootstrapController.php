<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BootstrapController extends Controller
{
    public function bootstrap()
    {
        $user = \Auth::user();

        return response()->json([
            'user' => $user,
        ]);
    }
}
