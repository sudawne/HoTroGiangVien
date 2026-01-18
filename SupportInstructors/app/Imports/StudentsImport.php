<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Exception;

class StudentsImport implements ToCollection, WithStartRow
{
    protected $class_id;

    public function __construct($class_id)
    {
        $this->class_id = $class_id;
    }

    public function startRow(): int
    {
        return 8; // Bắt đầu đọc từ dòng 8 như mẫu file của bạn
    }

    public function collection(Collection $rows)
    {
        $duplicates = [];
        $validRows = [];

        // BƯỚC 1: QUÉT TRÙNG LẶP
        foreach ($rows as $index => $row) {
            // Bỏ qua dòng trống hoặc không có mã SV
            if (!isset($row[1]) || empty(trim($row[1]))) continue;

            $mssv = trim($row[1]);

            // Kiểm tra trong Database xem mã này đã có chưa (Toàn hệ thống)
            if (Student::where('student_code', $mssv)->exists()) {
                // Lưu lại mã trùng để báo lỗi
                $duplicates[] = "$mssv (dòng " . ($index + 8) . ")";
            }

            $validRows[] = $row; // Lưu dòng hợp lệ vào mảng tạm
        }

        // BƯỚC 2: NẾU CÓ MÃ TRÙNG -> DỪNG NGAY LẬP TỨC
        if (!empty($duplicates)) {
            $errorMsg = "Không thể Import! Các mã sinh viên sau đã tồn tại trên hệ thống: " . implode(', ', $duplicates);
            throw new Exception($errorMsg);
        }

        // BƯỚC 3: NẾU KHÔNG TRÙNG -> TIẾN HÀNH LƯU
        foreach ($validRows as $row) {
            $mssv = trim($row[1]);
            // Ghép họ và tên
            $fullname = trim($row[2]) . ' ' . trim($row[3]);

            // Xử lý ngày sinh
            $dob = null;
            if (isset($row[5])) {
                try {
                    $dateStr = str_replace('/', '-', trim($row[5]));
                    $dob = Carbon::createFromFormat('d-m-Y', $dateStr)->format('Y-m-d');
                } catch (\Exception $e) {
                    $dob = null;
                }
            }

            // Xử lý trạng thái
            $statusRaw = trim($row[6] ?? '');
            $status = match ($statusRaw) {
                'Còn học' => 'studying',
                'Bảo lưu' => 'reserved',
                'Thôi học', 'Buộc thôi học' => 'dropped',
                'Tốt nghiệp' => 'graduated',
                default => 'studying'
            };

            // 1. Tạo User
            $user = User::create([
                'username' => $mssv,
                'name' => $fullname,
                'email' => $mssv . '@sv.kiengiang.edu.vn', // Email giả định
                'password' => Hash::make($mssv . '@123'),
                'role_id' => 3, // Role Student
                'is_active' => true
            ]);

            // 2. Tạo Student
            Student::create([
                'user_id' => $user->id,
                'class_id' => $this->class_id,
                'student_code' => $mssv,
                'fullname' => $fullname,
                'dob' => $dob,
                'status' => $status,
                'enrollment_year' => 2024, // Có thể sửa logic lấy năm động sau này
            ]);
        }
    }
}
