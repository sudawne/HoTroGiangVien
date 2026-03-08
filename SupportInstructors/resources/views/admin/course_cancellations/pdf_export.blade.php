<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Serif', serif; font-size: 11pt; }
        .title { text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 6px; text-align: left; font-size: 10pt; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="title">DANH SÁCH SINH VIÊN BỊ XÓA HỌC PHẦN</div>
    <div class="text-center" style="margin-bottom: 20px;">Ngày xuất: {{ date('d/m/Y') }}</div>

    <table>
        <thead>
            <tr>
                <th width="5%">STT</th>
                <th width="15%">MSSV</th>
                <th width="25%">Họ và Tên</th>
                <th width="10%">Lớp</th>
                <th width="25%">Môn bị xóa</th>
                <th width="10%">Lý do</th>
                <th width="10%">Tiền nợ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->student->student_code }}</td>
                    <td>{{ $item->student->fullname }}</td>
                    <td class="text-center">{{ $item->student->studentClass->code ?? '' }}</td>
                    <td>{{ $item->subject_name }} <br> <small>({{ $item->subject_code }})</small></td>
                    <td class="text-center">{{ $item->reason }}</td>
                    <td class="text-right">{{ number_format($item->debt_amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>