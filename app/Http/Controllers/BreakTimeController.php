<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BreakTimeController extends Controller
{
    public function index() 
    {
        return view('system/breaktime');
    }

    public function edit()
    {
        return view('system/edit/editbreaktime');
    }
}
