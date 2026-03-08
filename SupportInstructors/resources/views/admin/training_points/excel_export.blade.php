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
            <td colspan="5" style="text-align: center; font-size: 12pt; font-weight: bold;">
                CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM
            </td>
        </tr>
        <tr>
            <td colspan="3"
                style="text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline;">
                KHOA CÔNG NGHỆ THÔNG TIN
            </td>
            <td colspan="5"
                style="text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline;">
                Độc lập - Tự do - Hạnh phúc
            </td>
        </tr>
        <tr>
            <td colspan="8"></td> {{-- Dòng trống --}}
        </tr>
        <tr>
            <td colspan="8" style="text-align: center; font-size: 16pt; font-weight: bold;">
                DANH SÁCH ĐIỂM RÈN LUYỆN SINH VIÊN
            </td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: center; font-style: italic; font-size: 11pt;">
                (Ngày xuất báo cáo: {{ date('d/m/Y') }})
            </td>
        </tr>
        <tr>
            <td colspan="8"></td> {{-- Dòng trống --}}
        </tr>

        {{-- PHẦN TIÊU ĐỀ CÁC CỘT --}}
        <thead>
            <tr>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 60px;">STT</th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 120px;">MSSV</th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 250px;">Họ và tên
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 100px;">Lớp</th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 100px;">SV tự ĐG
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 100px;">Lớp ĐG</th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 100px;">Khoa Duyệt
                </th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 150px;">Xếp loại
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
                    <td style="text-align: center; border: 1px solid #000000;">{{ $item->self_score ?? '-' }}</td>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $item->class_score ?? '-' }}</td>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $item->final_score ?? '-' }}</td>
                    <td style="text-align: center; border: 1px solid #000000;">
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
                            Yếu
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>

        {{-- PHẦN CHỮ KÝ Ở CUỐI BẢNG --}}
        <tr>
            <td colspan="8"></td> {{-- Dòng trống --}}
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center; font-style: italic;">
                Kiên Giang, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center; font-weight: bold;">
                NGƯỜI LẬP BẢNG
            </td>
        </tr>
        <tr>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center; font-weight: bold;">
                {{ Auth::user()->name ?? '' }}
            </td>
        </tr>
    </table>
</body>

</html>
