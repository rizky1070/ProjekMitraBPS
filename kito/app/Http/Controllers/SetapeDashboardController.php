<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ketua;
use App\Models\Office;
use App\Models\User;

class SetapeDashboardController extends Controller
{
    public function index()
    {
        // Hitung statistik
        $stats = [
            'userCount' => User::count(),
            'adminUserCount' => User::where('is_admin', 1)->count(),
            'categoryCount' => Category::count(),
            'ketuaCount' => Ketua::count(),
            'ketuaActiveCount' => Ketua::active()->count(),
            'ketuaNonActiveCount' => Ketua::inactive()->count(),
            'officeCount' => Office::count(),
            'officeActiveCount' => Office::active()->count(),
            'officeNonActiveCount' => Office::inactive()->count(),
        ];

        return view('setape.dashboard', $stats);
    }
}