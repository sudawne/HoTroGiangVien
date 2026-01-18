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
        return 8;
    }

    public function collection(Collection $rows)
    {
        $duplicates = [];

        // 1. Kiểm tra trùng lặp trước
        foreach ($rows as $row) {
            if (!isset($row[1]) || empty($row[1])) continue;
            $mssv = trim($row[1]);

            // Kiểm tra xem MSSV đã tồn tại trong hệ thống chưa (bất kể lớp nào)
            if (Student::where('student_code', $mssv)->exists()) {
                $duplicates[] = $mssv;
            }
        }

        // Nếu có sinh viên trùng, dừng lại và báo lỗi ngay
        if (!empty($duplicates)) {
            $listMssv = implode(', ', $duplicates);
            throw new Exception("Không thể Import! Các mã sinh viên sau đã tồn tại trên hệ thống: " . $listMssv);
        }

        // 2. Nếu không trùng, tiến hành thêm mới
        foreach ($rows as $row) {
            if (!isset($row[1]) || empty($row[1])) continue;

            $mssv = trim($row[1]);
            $fullname = trim($row[2]) . ' ' . trim($row[3]);

            $dob = null;
            if (isset($row[5])) {
                try {
                    $dateStr = str_replace('/', '-', trim($row[5]));
                    $dob = Carbon::createFromFormat('d-m-Y', $dateStr)->format('Y-m-d');
                } catch (\Exception $e) {
                    $dob = null;
                }
            }

            $statusRaw = trim($row[6] ?? '');
            $status = match ($statusRaw) {
                'Còn học' => 'studying',
                'Bảo lưu' => 'reserved',
                'Thôi học', 'Buộc thôi học' => 'dropped',
                'Tốt nghiệp' => 'graduated',
                default => 'studying'
            };

            // Tạo User
            $user = User::create([
                'username' => $mssv,
                'name' => $fullname,
                'email' => $mssv . '@sv.kiengiang.edu.vn',
                'password' => Hash::make($mssv . '@123'),
                'role_id' => 3,
                'is_active' => true
            ]);

            // Tạo Sinh viên
            Student::create([
                'user_id' => $user->id,
                'class_id' => $this->class_id,
                'student_code' => $mssv,
                'fullname' => $fullname,
                'dob' => $dob,
                'status' => $status,
                'enrollment_year' => 2024,
            ]);
        }
    }
}
