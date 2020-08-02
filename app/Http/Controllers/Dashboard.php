<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    //This is the function that loads the dashboard page
    public function index(Request $request)
    {
        $data['menu_key'] = "MM_DASHBOARD";
        check_access(array($data['menu_key']));
    
        $data['message'] = "Dashboard Loaded";
        return view('dashboard.dashboard', $data);
    }
}
