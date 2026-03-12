<?php

namespace App\Imports;

use App\Models\CourseCancellation;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CourseCancellationsImport implements ToModel, WithHeadingRow
{
    protected $semesterId;

    public function __construct($semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function model(array $row)
    {

        if (!isset($row['ma_sinh_vien']) || !isset($row['ma_hoc_phan'])) {
            return null;
        }

        $student = Student::where('student_code', $row['ma_sinh_vien'])->first();

        if (!$student) {
            return null; 
        }

        return new CourseCancellation([
            'student_id'   => $student->id,
            'semester_id'  => $this->semesterId,
            'subject_code' => $row['ma_hoc_phan'],
            'subject_name' => $row['ten_hoc_phan'] ?? 'Không xác định',
            'reason'       => 'Nợ học phí', 
            'debt_amount'  => isset($row['no']) ? (float)str_replace([',', '.'], '', $row['no']) : 0,
        ]);
    }
}