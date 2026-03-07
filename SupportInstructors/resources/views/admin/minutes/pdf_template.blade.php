<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Biên bản Sinh hoạt lớp</title>
    <style>
        /* Cấu hình Font chữ hỗ trợ tiếng Việt Unicode */
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 13px; 
            line-height: 1.3; 
            margin: 0;
            padding: 0;
        }
        
        /* Layout Header 2 cột giống Word */
        table.header-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        table.header-table td { 
            vertical-align: top; 
            text-align: center; 
            padding: 0;
        }
        
        /* Cột trái (Trường + Khoa) */
        .header-left { width: 45%; }
        .school-name { font-size: 11px; }
        .dept-name { font-weight: bold; font-size: 11px; }
        
        /* Cột phải (Quốc hiệu) */
        .header-right { width: 55%; }
        .nation-name { font-weight: bold; font-size: 11px; }
        .motto-name { font-weight: bold; font-size: 11px; }
        
        /* Dòng kẻ dưới header */
        .line-break { margin: 0; padding: 0; font-weight: bold; }

        /* Tiêu đề chính */
        .main-title { 
            text-align: center; 
            font-weight: bold; 
            font-size: 16px; 
            margin-top: 15px; 
            margin-bottom: 5px; 
            text-transform: uppercase;
        }
        .sub-title { 
            text-align: center; 
            font-weight: bold; 
            font-size: 14px; 
            margin-bottom: 5px; 
        }
        .semester-info { 
            text-align: center; 
            font-style: italic; 
            margin-bottom: 15px; 
        }

        /* Các đề mục I, II, III... */
        .heading { 
            font-weight: bold; 
            font-size: 13px; 
            margin-top: 15px; 
            margin-bottom: 5px; 
        }

        /* Nội dung chi tiết */
        .content-block { 
            text-align: justify; 
            margin-bottom: 5px;
        }
        .bold { font-weight: bold; }
        
        /* Indent giống Word */
        .indent { margin-left: 20px; }

        /* Bảng chữ ký cuối trang */
        table.footer-table { 
            width: 100%; 
            margin-top: 30px; 
            border-collapse: collapse;
        }
        table.footer-table td { 
            text-align: center; 
            vertical-align: top; 
            width: 50%;
        }
        .sign-title { font-weight: bold; text-transform: uppercase; }
        .sign-note { font-style: italic; font-size: 11px; }
        .signer-name { font-weight: bold; margin-top: 60px; } /* Khoảng trống ký tên */
    </style>
</head>
<body>

    {{-- HEADER 2 CỘT --}}
    <table class="header-table">
        <tr>
            <td class="header-left">
                <div class="school-name">TRƯỜNG ĐẠI HỌC KIÊN GIANG</div>
                <div class="dept-name">KHOA THÔNG TIN TRUYỀN THÔNG</div>
                <div class="line-break">_______________________</div>
            </td>
            <td class="header-right">
                <div class="nation-name">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                <div class="motto-name">Độc lập - Tự do - Hạnh phúc</div>
                <div class="line-break">_______________________</div>
            </td>
        </tr>
    </table>

    {{-- TIÊU ĐỀ --}}
    @php
        $tenHocKyDB = $minute->semester->name ?? ''; 
        $soHocKy = trim(str_ireplace('Học kỳ', '', $tenHocKyDB));
    @endphp
    <div class="main-title">BIÊN BẢN HỌP LỚP</div>
    <div class="sub-title">{{ $minute->title }}</div>
    <div class="semester-info">
        Học kỳ: {{ $soHocKy ?? '...' }} Năm học: {{ $minute->semester->academic_year ?? '...' }}
    </div>

    {{-- MỤC I --}}
    <div class="heading">I. THỜI GIAN, ĐỊA ĐIỂM, THÀNH PHẦN THAM DỰ</div>
    
    <div class="content-block">
        <span class="bold">1. Thời gian:</span> {{ $minute->held_at ? $minute->held_at->format('H:i \n\g\à\y d/m/Y') : '...' }}
    </div>
    
    <div class="content-block">
        <span class="bold">2. Địa điểm:</span> {{ $minute->location }}
    </div>

    <div class="content-block">
        <span class="bold">3. Thành phần tham dự</span>
    </div>
    <div class="indent">
        - Cố vấn: {{ $minute->studentClass->advisor->user->name ?? '' }}<br>
        - Chủ trì: {{ $minute->monitor->fullname ?? '' }}<br>
        - Thư ký: {{ $minute->secretary->fullname ?? '' }}<br>
        - Tổng số: {{ $minute->attendees_count + count($minute->absent_list ?? []) }}; 
        Có mặt: {{ $minute->attendees_count }}; 
        Vắng: {{ count($minute->absent_list ?? []) }}
    </div>

    {{-- MỤC II --}}
    <div class="heading">II. NỘI DUNG</div>
    <div class="content-block">
        {!! $minute->content_discussions !!}
    </div>

    {{-- MỤC III --}}
    <div class="heading">III. KẾT LUẬN</div>
    <div class="content-block">
        {!! $minute->content_conclusion !!}
    </div>

    {{-- MỤC IV --}}
    <div class="heading">IV. KIẾN NGHỊ</div>
    <div class="content-block">
        {!! $minute->content_requests !!}
    </div>

    {{-- KẾT THÚC --}}
    <div class="content-block" style="margin-top: 10px;">
        Cuộc họp kết thúc lúc vào lúc {{ $minute->ended_at ? $minute->ended_at->format('H:i') : '...' }} cùng ngày./.
    </div>

    {{-- CHỮ KÝ --}}
    <table class="footer-table">
        <tr>
            <td>
                <div class="sign-title">THƯ KÝ</div>
                <div class="sign-note">(Ký và ghi rõ họ tên)</div>
                <div class="signer-name">{{ $minute->secretary->fullname ?? '' }}</div>
            </td>
            <td>
                <div class="sign-title">CỐ VẤN HỌC TẬP</div>
                <div class="sign-note">(Ký và ghi rõ họ tên)</div>
                <div class="signer-name">{{ $minute->studentClass->advisor->user->name ?? '' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>