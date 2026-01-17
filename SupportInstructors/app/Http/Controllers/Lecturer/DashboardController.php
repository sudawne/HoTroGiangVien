<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('lecturer.dashboard');
    }
}
