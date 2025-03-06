<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InputMitraBpsController extends Controller
{
    public function index()
    {
        return view('mitrabps\inputmitrabps'); // Memanggil view mitrabps.blade.php
    }

}
