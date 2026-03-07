<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Danh sách Cảnh báo Học tập</title>
    <style>
        body { 
            font-family: 'DejaVu Serif', serif; 
            font-size: 11pt; 
        }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { text-align: center; vertical-align: top; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .main-title { 
            text-align: center; 
            font-size: 16pt; 
            font-weight: bold; 
            margin-bottom: 20px; 
            text-transform: uppercase; 
        }

        /* Bảng dữ liệu chính */
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .data-table th, .data-table td { 
            border: 1px solid black; 
            padding: 6px; 
            text-align: left; 
            font-size: 10pt; 
        }
        .data-table th { 
            background-color: #f0f0f0; 
            text-align: center; 
            font-weight: bold; 
        }
        .text-center { text-align: center !important; }
        .text-danger { color: #dc2626; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td width="40%">
                <div style="font-size: 10pt;">TRƯỜNG ĐẠI HỌC KIÊN GIANG</div>
                <div class="font-bold" style="font-size: 10pt;">KHOA CÔNG NGHỆ THÔNG TIN</div>
            </td>
            <td width="60%">
                <div class="font-bold" style="font-size: 10pt;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                <div class="font-bold" style="font-size: 10pt;">Độc lập - Tự do - Hạnh phúc</div>
            </td>
        </tr>
    </table>

    <div class="main-title">DANH SÁCH SINH VIÊN BỊ CẢNH BÁO HỌC TẬP</div>

    <div style="text-align: center; margin-bottom: 20px; font-style: italic;">
        (Ngày xuất báo cáo: {{ date('d/m/Y') }})
    </div>

    {{-- TABLE --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">STT</th>
                <th width="15%">MSSV</th>
                <th width="25%">Họ và Tên</th>
                <th width="10%">Lớp</th>
                <th width="15%">Mức cảnh báo</th>
                <th width="10%">GPA Kỳ</th>
                <th width="20%">Lý do</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $warning)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $warning->student->student_code }}</td>
                    <td>{{ $warning->student->fullname }}</td>
                    <td class="text-center">{{ $warning->student->class->code ?? '' }}</td>
                    
                    <td class="text-center font-bold {{ $warning->warning_level >= 3 ? 'text-danger' : '' }}">
                        @if($warning->warning_level >= 3)
                            Buộc thôi học
                        @else
                            Mức {{ $warning->warning_level }}
                        @endif
                    </td>
                    
                    <td class="text-center">{{ number_format($warning->gpa_term ?? 0, 2) }}</td>
                    <td>{{ $warning->reason }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <table class="header-table" style="margin-top: 30px;">
        <tr>
            <td width="50%"></td>
            <td width="50%">
                <div style="font-style: italic;">Kiên Giang, ngày ... tháng ... năm ......</div>
                <div class="font-bold" style="margin-top: 5px;">NGƯỜI LẬP BẢNG</div>
                <div style="height: 80px;"></div>
                <div class="font-bold">{{ Auth::user()->name ?? '' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>