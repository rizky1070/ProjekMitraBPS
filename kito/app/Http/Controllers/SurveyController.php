<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Survey;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SuratTugas;
use Illuminate\Http\RedirectResponse;



class SurveyController extends Controller
{
    // Display a listing of the survey
    public function index()
    {
        // $survey = SurveyController::call();
        return view('mitrabps.survey');
    }
}