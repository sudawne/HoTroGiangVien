<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeetingMinuteController extends Controller
{
    public function index()
    {
        return view('admin.minutes.index');
    }

    public function create()
    {
        return view('admin.minutes.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.minutes.index');
    }

    public function show(string $id)
    {
        return view('admin.minutes.show');
    }

    public function edit(string $id)
    {
        return view('admin.minutes.edit');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.minutes.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.minutes.index');
    }
}
