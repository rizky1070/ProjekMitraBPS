<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Link;
use App\Models\User;
use App\Models\Office;
use App\Models\Ketua;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $id = Auth::id();
        $users = User::all();
        $admin = User::where('roles', 'ADMIN');
        $links = Office::where('status','1')->count();
        $mlinks = Office::where('status','0')->count();
        $lketua= Ketua::where ('status','1')->count();
        $mketua= Ketua::where ('status','0')->count();
        $linkuser = Link::where('user_id', $id)->get();
        return view('page.admin.admin', compact('users', 'links', 'linkuser', 'admin','lketua','mlinks','mketua'));
    }
}
