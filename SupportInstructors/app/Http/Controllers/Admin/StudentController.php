<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('admin.students.index');
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.students.index');
    }

    public function show(string $id)
    {
        return view('admin.students.show');
    }

    public function edit(string $id)
    {
        return view('admin.students.edit');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.students.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.students.index');
    }
}
