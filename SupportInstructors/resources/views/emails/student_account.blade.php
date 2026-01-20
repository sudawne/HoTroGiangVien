<!DOCTYPE html>
<html>

<head>
    <title>Thông tin tài khoản sinh viên</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Xin chào {{ $studentName }},</h2>
    <p>Bạn đã được thêm vào hệ thống Cố vấn Học tập - Khoa CNTT.</p>
    <p>Dưới đây là thông tin đăng nhập của bạn:</p>
    <ul>
        <li><strong>Tên đăng nhập (MSSV):</strong> {{ $username }}</li>
        <li><strong>Mật khẩu:</strong> {{ $password }}</li>
    </ul>
    <p>Vui lòng đăng nhập và đổi mật khẩu ngay trong lần đầu tiên.</p>
    <p>Trân trọng,<br>Hệ thống Quản lý.</p>
</body>

</html>
