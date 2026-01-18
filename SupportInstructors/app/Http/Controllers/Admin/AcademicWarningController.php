<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AcademicWarningController extends Controller
{
   public function index()
    {
        // Hiện tại trả về view tĩnh, sau này sẽ query dữ liệu từ DB tại đây
        return view('admin.academic_warnings.index');
    }
}
