<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportMitraSurveiController extends Controller
{
    public function SurveiReport()
    {
        return view('mitrabps.reportMitra');
    }

    public function MitraReport()
    {
        return view('mitrabps.reportSurvei');
    }
}
