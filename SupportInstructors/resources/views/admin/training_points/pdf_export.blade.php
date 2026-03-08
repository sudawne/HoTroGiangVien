<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Danh sách Điểm Rèn Luyện Sinh Viên</title>
    <style>
        body {
            font-family: 'DejaVu Serif', serif;
            font-size: 11pt;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            text-align: center;
            vertical-align: top;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

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

        .data-table th,
        .data-table td {
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

        .text-center {
            text-align: center !important;
        }

        .text-danger {
            color: #dc2626;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td width="40%">
                <div style="font-size: 10pt;">TRƯỜNG ĐẠI HỌC KIÊN GIANG</div>
                <div class="font-bold" style="font-size: 10pt;">KHOA THÔNG TIN VÀ TRUYỀN THÔNG</div>
            </td>
            <td width="60%">
                <div class="font-bold" style="font-size: 10pt;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                <div class="font-bold" style="font-size: 10pt;">Độc lập - Tự do - Hạnh phúc</div>
            </td>
        </tr>
    </table>

    <div class="main-title">DANH SÁCH ĐIỂM RÈN LUYỆN SINH VIÊN</div>

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
                <th width="10%">SV tự ĐG</th>
                <th width="10%">Lớp ĐG</th>
                <th width="10%">Khoa duyệt</th>
                <th width="15%">Xếp loại</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->student->student_code ?? 'N/A' }}</td>
                    <td>{{ $item->student->fullname ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->student->studentClass->code ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->self_score ?? '-' }}</td>
                    <td class="text-center">{{ $item->class_score ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $item->final_score ?? '-' }}</td>
                    <td
                        class="text-center font-bold 
                        {{ $item->final_score >= 90 ? 'text-blue-600' : ($item->final_score < 50 ? 'text-danger' : '') }}">
                        @if (is_null($item->final_score))
                            Chưa xét
                        @elseif($item->final_score >= 90)
                            Xuất sắc
                        @elseif($item->final_score >= 80)
                            Tốt
                        @elseif($item->final_score >= 65)
                            Khá
                        @elseif($item->final_score >= 50)
                            Trung bình
                        @else
                            Yếu/Kém
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <table class="header-table" style="margin-top: 30px;">
        <tr>
            <td width="50%"></td>
            <td width="50%">
                <div style="font-style: italic;">Kiên Giang, ngày {{ date('d') }} tháng {{ date('m') }} năm
                    {{ date('Y') }}</div>
                <div class="font-bold" style="margin-top: 5px;">NGƯỜI LẬP BẢNG</div>
                <div style="height: 80px;"></div>
                <div class="font-bold">{{ Auth::user()->name ?? 'Ban Chủ Nhiệm Khoa' }}</div>
            </td>
        </tr>
    </table>

</body>

</html>
