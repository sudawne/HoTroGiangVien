<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function showImportClass($id)
    {
        $class = Classes::findOrFail($id);
        return view('admin.classes.import', compact('class'));
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240',
            'class_id' => 'required|exists:classes,id',
        ]);

        try {
            $class = Classes::findOrFail($request->class_id);
            $path = $request->file('file')->store('temp');
            $data = Excel::toArray(new StudentsImport($class->id), $path);
            $rows = $data[0] ?? [];
            $previewData = array_slice($rows, 7);

            $formattedPreview = [];
            foreach ($previewData as $index => $row) {
                if (!isset($row[1]) || empty(trim($row[1]))) continue;

                $mssv = trim($row[1]);
                $exists = \App\Models\Student::where('student_code', $mssv)->exists();

                $formattedPreview[] = [
                    'mssv' => $mssv,
                    'name' => trim($row[2]) . ' ' . trim($row[3]),
                    'dob' => $row[5] ?? '',
                    'status' => $row[6] ?? '',
                    'is_duplicate' => $exists
                ];
            }

            $html = view('admin.classes.partials.preview_table', ['previewData' => $formattedPreview])->render();

            return response()->json([
                'html' => $html,
                'temp_path' => $path,
                'hasError' => collect($formattedPreview)->contains('is_duplicate', true)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi đọc file: ' . $e->getMessage()], 500);
        }
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'temp_path' => 'required',
        ]);

        try {
            $shouldSendEmail = $request->boolean('send_email');
            Excel::import(new StudentsImport($request->class_id, $shouldSendEmail), $request->temp_path);
            Storage::delete($request->temp_path);

            return redirect()->route('admin.classes.index')
                ->with('success', 'Đã import danh sách sinh viên và tạo tài khoản thành công!');
        } catch (\Exception $e) {
            Storage::delete($request->temp_path);
            return redirect()->back()->with('error', 'Lỗi Import: ' . $e->getMessage());
        }
    }
}
