<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        return view('admin.classes.index');
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.classes.index');
    }


    public function show(string $id)
    {
        return view('admin.classes.show');
    }


    public function edit(string $id)
    {
        return view('admin.classes.edit');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.classes.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.classes.index');
    }
}
