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
    // 1. Hiển thị trang Import cho một lớp cụ thể
    public function showImportClass($id)
    {
        $class = Classes::findOrFail($id);
        return view('admin.classes.import', compact('class'));
    }

    // 2. Xử lý Preview (Xem trước)
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240',
            'class_id' => 'required|exists:classes,id',
        ]);

        try {
            $class = Classes::findOrFail($request->class_id);

            // Lưu file tạm thời để bước sau dùng lại
            $path = $request->file('file')->store('temp');

            // Đọc dữ liệu ra mảng để hiển thị (chưa lưu vào DB)
            // StudentsImport cần implement ToArray hoặc dùng Excel::toArray
            $data = Excel::toArray(new StudentsImport($class->id), $path);

            // Giả sử dữ liệu ở sheet đầu tiên [0]
            $rows = $data[0] ?? [];

            // Loại bỏ các dòng trống hoặc header (logic tùy file excel của bạn)
            // Ví dụ file của bạn bắt đầu data từ dòng 8 (index 7 trong mảng)
            $previewData = array_slice($rows, 7);

            return view('admin.classes.import', [
                'class' => $class,
                'previewData' => $previewData,
                'tempPath' => $path // Truyền đường dẫn file tạm để bước sau import
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi đọc file: ' . $e->getMessage());
        }
    }

    // 3. Xử lý Lưu chính thức
    public function storeImport(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'temp_path' => 'required',
        ]);

        try {
            // Import từ file tạm
            Excel::import(new StudentsImport($request->class_id), $request->temp_path);

            // Xóa file tạm sau khi xong
            Storage::delete($request->temp_path);

            return redirect()->route('admin.classes.index')
                ->with('success', 'Đã import danh sách sinh viên thành công!');
        } catch (\Exception $e) {
            // Xóa file tạm nếu lỗi
            Storage::delete($request->temp_path);
            return redirect()->route('admin.classes.import', $request->class_id)
                ->with('error', 'Lỗi Import: ' . $e->getMessage());
        }
    }
}
