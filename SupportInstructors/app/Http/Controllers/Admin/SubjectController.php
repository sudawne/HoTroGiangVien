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

    // Xử lý sửa
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:subjects,code,' . $id, 
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:0',
        ], [
            'code.required' => 'Vui lòng nhập mã môn học.',
            'code.unique' => 'Mã môn học này đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên môn học.',
            'credits.required' => 'Vui lòng nhập số tín chỉ.',
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update([
            'code' => $request->code,
            'name' => $request->name,
            'credits' => $request->credits,
        ]);

        return back()->with('success', 'Đã cập nhật môn học thành công.');
    }

    // Xử lý Xóa
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        // if ($subject->classes()->count() > 0) {
        //     return back()->with('error', 'Không thể xóa môn học đang có lớp học phần.');
        // }
        $subject->delete();

        return back()->with('success', 'Đã xóa môn học thành công.');
    }
    public function quickStore(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:subjects,code',
            'name' => 'required',
            'credits' => 'required|integer|min:0'
        ]);

        try {
            $subject = Subject::create([
                'code' => $request->code,
                'name' => $request->name,
                'credits' => $request->credits
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm môn học thành công!',
                'subject_id' => $subject->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}