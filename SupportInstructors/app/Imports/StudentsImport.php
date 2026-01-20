<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\StudentAccountCreated;
use Exception;
use Illuminate\Support\Facades\Log;

class StudentsImport implements ToCollection, WithStartRow
{
    protected $class_id;
    protected $sendEmail;
    public $newStudentIds = []; // Mảng lưu ID sinh viên vừa tạo

    public function __construct($class_id, $sendEmail = false)
    {
        $this->class_id = $class_id;
        $this->sendEmail = $sendEmail;
    }

    public function startRow(): int
    {
        return 8;
    }

    public function collection(Collection $rows)
    {
        $duplicates = [];
        $validRows = [];

        foreach ($rows as $index => $row) {
            if (!isset($row[1]) || empty(trim($row[1]))) continue;
            $mssv = trim($row[1]);
            if (Student::where('student_code', $mssv)->exists()) {
                $duplicates[] = "$mssv (dòng " . ($index + 8) . ")";
            }
            $validRows[] = $row;
        }

        if (!empty($duplicates)) {
            $errorMsg = "Không thể Import! Các mã sinh viên sau đã tồn tại: " . implode(', ', $duplicates);
            throw new Exception($errorMsg);
        }

        foreach ($validRows as $row) {
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

            $parts = explode(' ', $fullname);
            $firstName = array_pop($parts);
            $slugName = Str::slug($firstName, '');
            $email = $slugName . $mssv . '@vnkgu.edu.vn';
            $rawPassword = $slugName . $mssv;

            $user = User::where('username', $mssv)->orWhere('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $fullname,
                    'email' => $email,
                    'username' => $mssv,
                    'password' => Hash::make($rawPassword),
                    'role_id' => 3,
                    'is_active' => true,
                ]);

                if ($this->sendEmail) {
                    try {
                        Mail::to($email)->send(new StudentAccountCreated($fullname, $mssv, $rawPassword));
                    } catch (\Exception $e) {
                        Log::error("Failed to send email to $email: " . $e->getMessage());
                    }
                }
            }

            // Lưu ID của sinh viên mới vào mảng
            $student = Student::create([
                'user_id' => $user->id,
                'class_id' => $this->class_id,
                'student_code' => $mssv,
                'fullname' => $fullname,
                'dob' => $dob,
                'status' => $status,
                'enrollment_year' => 2024,
            ]);

            $this->newStudentIds[] = $student->id;
        }
    }
}
