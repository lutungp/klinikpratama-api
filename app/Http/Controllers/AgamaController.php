<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgamaController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getAgama()
    {
        return response()->json(['pesan' => "test"], 200);
    }
}
