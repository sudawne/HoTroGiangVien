<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\User;
use App\Models\Department;
use App\Http\Requests\StoreLecturerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\LecturerAccountCreated; // Import Mail Class
use Illuminate\Support\Str;

class LecturerController extends Controller
{
    public function index(Request $request)
    {
        $query = Lecturer::with(['user', 'department']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('lecturer_code', 'like', "%{$search}%");
        }

        if ($request->has('department_id') && $request->department_id != 'all') {
            $query->where('department_id', $request->department_id);
        }

        $lecturers = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $departments = Department::all();

        return view('admin.lecturers.index', compact('lecturers', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.lecturers.create', compact('departments'));
    }

    public function store(StoreLecturerRequest $request)
    {
        DB::beginTransaction();
        try {
            // 1. Xử lý upload ảnh
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                // Tham số thứ 2 là 'public' để chỉ định lưu vào storage/app/public
                $path = $request->file('avatar')->store('avatars', 'public');

                // Lúc này $path sẽ trả về "avatars/ten_anh.jpg" luôn, không cần str_replace nữa
                $avatarPath = $path;
            }

            // 2. Xử lý Tên và Email
            $fullName = trim($request->name);
            $email = $request->email;

            // Xử lý tên để lấy phần Tên (FirstName) và Họ đệm (Initials)
            // Hàm vn_to_str cần được định nghĩa bên dưới (private function)
            $nameUnaccent = $this->vn_to_str($fullName);
            $nameParts = explode(' ', $nameUnaccent);
            $lastName = array_pop($nameParts); // Tên (ví dụ: Nhan)

            // Nếu không nhập email => Tự động tạo
            if (empty($email)) {
                $initials = '';
                foreach ($nameParts as $part) {
                    $initials .= substr($part, 0, 1); // Lấy chữ cái đầu của họ và đệm (v, h)
                }
                // Email = vhnhan@vnkgu.edu.vn
                $emailPrefix = strtolower($initials . $lastName);
                $email = $emailPrefix . '@vnkgu.edu.vn';

                // Kiểm tra trùng email (nếu trùng thì thêm số ngẫu nhiên)
                if (User::where('email', $email)->exists()) {
                    $email = $emailPrefix . rand(10, 99) . '@vnkgu.edu.vn';
                }
            }

            // 3. Xử lý Mật khẩu tự động: Tên + Mã GV (ví dụ: nhanGV001)
            $rawPassword = strtolower($lastName) . $request->lecturer_code;
            $hashedPassword = Hash::make($rawPassword);

            // 4. Tạo User
            $user = User::create([
                'name' => $fullName,
                'email' => $email,
                'phone' => $request->phone,
                'username' => $request->lecturer_code,
                'password' => $hashedPassword,
                'role_id' => 2, // 2 = Lecturer
                'is_active' => true,
                'avatar_url' => $avatarPath,
            ]);

            // 5. Tạo Lecturer
            Lecturer::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'lecturer_code' => $request->lecturer_code,
                'degree' => $request->degree,
                'position' => $request->position ?? 'Giảng viên',
            ]);

            DB::commit();

            // 6. Gửi Email (Sử dụng Try-Catch riêng để không chặn quy trình tạo nếu lỗi mail)
            try {
                // HARDCODE EMAIL NHẬN ĐỂ TEST
                $testRecipient = 'nguyen22082006204@vnkgu.edu.vn';

                // Sau này hoàn thành thì đổi thành: $recipient = $user->email;
                Mail::to($testRecipient)->send(new LecturerAccountCreated($user, $rawPassword));
            } catch (\Exception $mailEx) {
                // Log lỗi mail nhưng không rollback DB
                Log::error('Lỗi gửi mail giảng viên: ' . $mailEx->getMessage());
            }

            return redirect()->route('admin.lecturers.index')
                ->with('success', "Thêm giảng viên thành công! Email: $email, Mật khẩu: $rawPassword");
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($path)) Storage::delete($path);
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    // ... (Các hàm index, edit, update, destroy giữ nguyên) ...
    public function edit($id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $departments = Department::all();
        return view('admin.lecturers.edit', compact('lecturer', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $user = $lecturer->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'lecturer_code' => 'required|string|unique:lecturers,lecturer_code,' . $lecturer->id,
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'username' => $request->lecturer_code,
            ]);

            $lecturer->update([
                'department_id' => $request->department_id,
                'lecturer_code' => $request->lecturer_code,
                'degree' => $request->degree,
                'position' => $request->position,
            ]);

            DB::commit();
            return redirect()->route('admin.lecturers.index')->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        if (\App\Models\Classes::where('advisor_id', $id)->exists()) {
            return back()->with('error', 'Không thể xóa! Giảng viên này đang là Cố vấn học tập của một lớp.');
        }
        $user = $lecturer->user;
        $lecturer->delete();
        if ($user) $user->delete();

        return redirect()->route('admin.lecturers.index')->with('success', 'Đã xóa giảng viên.');
    }

    // Hàm hỗ trợ chuyển tiếng Việt sang không dấu (để tạo mail/pass)
    private function vn_to_str($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return strtolower($str); // Trả về chữ thường không dấu
    }
}
