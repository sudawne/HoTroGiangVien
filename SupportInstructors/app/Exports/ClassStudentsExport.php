<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClassStudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $classId;

    public function __construct($classId)
    {
        $this->classId = $classId;
    }

    public function collection()
    {
        return Student::with('user')
            ->where('class_id', $this->classId)
            ->orderBy('student_code', 'asc')
            ->get();
    }

    public function map($student): array
    {
        return [
            $student->student_code,
            $student->fullname,
            $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '',
            $student->user->email ?? '',
            $student->status == 'studying' ? 'Đang học' : $student->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Mã Sinh Viên',
            'Họ và Tên',
            'Ngày Sinh',
            'Email Hệ Thống',
            'Trạng Thái',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
