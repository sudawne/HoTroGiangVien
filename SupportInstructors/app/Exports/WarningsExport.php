<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarningsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $warnings;

    public function __construct($warnings)
    {
        $this->warnings = $warnings;
    }

    public function collection()
    {
        return $this->warnings;
    }

    // Tiêu đề các cột
    public function headings(): array
    {
        return [
            'STT',
            'Mã Sinh Viên',
            'Họ và Tên',
            'Lớp Sinh Hoạt',
            'Học Kỳ',
            'Mức Cảnh Báo',
            'Điểm TB (Hệ 4)',
            'Số TC Nợ',
            'Lý Do / Ghi Chú'
        ];
    }

    // Ánh xạ dữ liệu từng dòng
    public function map($warning): array
    {
        static $index = 0;
        $index++;

        // Xử lý hiển thị mức cảnh báo
        $levelText = match ($warning->warning_level) {
            1 => 'Mức 1',
            2 => 'Mức 2',
            3 => 'Buộc thôi học',
            default => 'Mức ' . $warning->warning_level
        };

        return [
            $index,
            $warning->student->student_code ?? '',
            $warning->student->fullname ?? '',
            $warning->student->class->code ?? '',
            $warning->semester->name ?? '' . ' (' . ($warning->semester->academic_year ?? '') . ')',
            $levelText,
            $warning->gpa_term ?? '0.00', // Giả sử bạn lưu GPA kỳ này trong bảng warning
            $warning->credits_owed ?? '0', // Giả sử lưu số tín chỉ nợ
            $warning->reason ?? '',
        ];
    }

    // Style cho file Excel đẹp hơn (In đậm header)
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}