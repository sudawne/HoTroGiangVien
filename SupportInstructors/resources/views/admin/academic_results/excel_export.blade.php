<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
        }
    </style>
</head>

<body>
    <table>
        {{-- PHẦN HEADER BÁO CÁO CỦA EXCEL --}}
        <tr>
            <td colspan="3" style="text-align: center; font-size: 12pt;">
                TRƯỜNG ĐẠI HỌC KIÊN GIANG
            </td>
            <td colspan="4" style="text-align: center; font-size: 12pt; font-weight: bold;">
                CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM
            </td>
        </tr>
        <tr>
            <td colspan="3"
                style="text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline;">
                KHOA CÔNG NGHỆ THÔNG TIN
            </td>
            <td colspan="4"
                style="text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline;">
                Độc lập - Tự do - Hạnh phúc
            </td>
        </tr>
        <tr>
            <td colspan="7"></td> {{-- Dòng trống --}}
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; font-size: 16pt; font-weight: bold;">
                DANH SÁCH KẾT QUẢ HỌC TẬP SINH VIÊN
            </td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; font-style: italic; font-size: 11pt;">
                (Ngày xuất báo cáo: {{ date('d/m/Y') }})
            </td>
        </tr>
        <tr>
            <td colspan="7"></td> {{-- Dòng trống --}}
        </tr>

        {{-- PHẦN TIÊU ĐỀ CÁC CỘT --}}
        <thead>
            <tr>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 60px;">
                    STT
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 120px;">
                    MSSV
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 250px;">
                    Họ và tên
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 100px;">
                    Lớp
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 100px;">
                    GPA (Hệ 10)
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 100px;">
                    GPA (Hệ 4)
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 150px;">
                    Xếp loại
                </th>
            </tr>
        </thead>

        {{-- PHẦN DỮ LIỆU --}}
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $index + 1 }}</td>
                    <td style="text-align: center; border: 1px solid #000000;">
                        {{ $item->student->student_code ?? 'N/A' }}</td>
                    <td style="text-align: left; border: 1px solid #000000;">{{ $item->student->fullname ?? 'N/A' }}
                    </td>
                    <td style="text-align: center; border: 1px solid #000000;">
                        {{ $item->student->studentClass->code ?? 'N/A' }}</td>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $item->gpa_10 ?? '-' }}</td>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $item->gpa_4 ?? '-' }}</td>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $item->classification ?? 'Chưa xét' }}
                    </td>
                </tr>
            @endforeach
        </tbody>

        {{-- PHẦN CHỮ KÝ Ở CUỐI BẢNG --}}
        <tr>
            <td colspan="7"></td> {{-- Dòng trống --}}
        </tr>
        <tr>
            <td colspan="4"></td>
            <td colspan="3" style="text-align: center; font-style: italic;">
                Kiên Giang, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td colspan="3" style="text-align: center; font-weight: bold;">
                NGƯỜI LẬP BẢNG
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td colspan="3" style="text-align: center; font-weight: bold;">
                {{ Auth::user()->name ?? '' }}
            </td>
        </tr>
    </table>
</body>

</html>
