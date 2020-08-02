<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Search extends Controller
{

    public function index()
    {
        $data['menu_key'] = '';
        $data['sub_menu_key'] = '';
        return view('search.search', $data);
    }
}