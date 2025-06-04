<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ketua;
use App\Models\Office;
use App\Models\User;
use App\Models\CategoryUser;
use App\Models\Link;
use Illuminate\Support\Facades\Auth;

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
            'linkPribadiCount' => Link::where('user_id', Auth::id())->count(),
            'categoryPribadiCount' => CategoryUser::where('user_id', Auth::id())->count(),
        ];

        return view('setape.dashboard', $stats);
    }
}
