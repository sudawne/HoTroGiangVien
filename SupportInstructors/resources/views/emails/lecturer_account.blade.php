<!DOCTYPE html>
<html>

<head>
    <title>Thông tin tài khoản giảng viên</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Xin chào Quý thầy/cô {{ $user->name }},</h2>
    <p>Thầy/cô đã được thêm vào hệ thống Cố vấn Học tập - Khoa Thông tin & Truyền thông.</p>
    <p>Dưới đây là thông tin đăng nhập của thầy/cô:</p>
    <ul>
        <li><strong>Tên đăng nhập (Mã GV):</strong> {{ $user->username }}</li>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Mật khẩu:</strong> {{ $rawPassword }}</li>
    </ul>
    <p>Vui lòng đăng nhập và đổi mật khẩu ngay trong lần đầu tiên sử dụng.</p>
    <p>Trân trọng,<br>Hệ thống Hổ trợ Cố vấn Học tập.</p>
    <p>ĐÂY LÀ MAIL TEST PHỤC VỤ CHO NGHIÊN CỨU KHOA HỌC XIN VUI LÒNG BỎ QUA MAIL NÀY</p>
</body>

</html>
