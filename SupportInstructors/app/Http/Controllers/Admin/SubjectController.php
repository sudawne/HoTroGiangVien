<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $subjects = $query->orderBy('code', 'asc')->paginate(10)->withQueryString();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:subjects,code|max:20',
            'name' => 'required|max:255',
            'credits' => 'required|integer|min:0',
        ], [
            'code.unique' => 'Mã môn học này đã tồn tại.',
            'credits.integer' => 'Số tín chỉ phải là số nguyên.'
        ]);

        Subject::create($request->all());

        return redirect()->route('admin.subjects.index')->with('success', 'Thêm môn học thành công.');
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        
        $request->validate([
            'code' => 'required|max:20|unique:subjects,code,' . $id,
            'name' => 'required|max:255',
            'credits' => 'required|integer|min:0',
        ]);

        $subject->update($request->all());

        return redirect()->route('admin.subjects.index')->with('success', 'Cập nhật thành công.');
    }

    public function destroy($id)
    {
        Subject::destroy($id);
        return redirect()->route('admin.subjects.index')->with('success', 'Đã xóa môn học.');
    }
}