<?php

namespace App\Http\Controllers\Admin;

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
use App\Mail\LecturerAccountCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LecturerController extends Controller
{
    public function index(Request $request)
    {
        // QUAN TRỌNG: withTrashed() trong closure của User để lấy được user đã bị xóa mềm
        $query = Lecturer::with(['user' => function ($q) {
            $q->withTrashed();
        }, 'department']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->withTrashed()
                    ->where(function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
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
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $avatarPath = $path;
            }

            $fullName = trim($request->name);
            $email = $request->email;
            $nameUnaccent = $this->vn_to_str($fullName);
            $nameParts = explode(' ', $nameUnaccent);
            $lastName = array_pop($nameParts);

            if (empty($email)) {
                $initials = '';
                foreach ($nameParts as $part) {
                    if (!empty($part)) $initials .= substr($part, 0, 1);
                }
                $emailPrefix = strtolower($initials . $lastName);
                $email = $emailPrefix . '@vnkgu.edu.vn';
                if (User::where('email', $email)->exists()) {
                    $email = $emailPrefix . rand(10, 99) . '@vnkgu.edu.vn';
                }
            }

            $rawPassword = strtolower($lastName) . $request->lecturer_code;
            $hashedPassword = Hash::make($rawPassword);

            $user = User::create([
                'name' => $fullName,
                'email' => $email,
                'phone' => $request->phone,
                'username' => $request->lecturer_code,
                'password' => $hashedPassword,
                'role_id' => 2,
                'is_active' => true,
                'avatar_url' => $avatarPath,
            ]);

            $newLecturer = Lecturer::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'lecturer_code' => $request->lecturer_code,
                'degree' => $request->degree,
                'position' => $request->position ?? 'Giảng viên',
            ]);

            DB::commit();

            try {
                $testRecipient = 'nguyen22082006204@vnkgu.edu.vn';
                Mail::to($testRecipient)->send(new LecturerAccountCreated($user, $rawPassword));
            } catch (\Exception $mailEx) {
                Log::error('Lỗi gửi mail giảng viên: ' . $mailEx->getMessage());
            }

            return redirect()->route('admin.lecturers.index')
                ->with('success', "Thêm giảng viên thành công!")
                ->with('highlight_id', $newLecturer->id);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($path)) Storage::disk('public')->delete($path);
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        // Lấy Lecturer và User (kể cả đã xóa mềm để sửa thông tin nếu cần)
        $lecturer = Lecturer::where('id', $id)->with(['user' => function ($q) {
            $q->withTrashed();
        }])->firstOrFail();

        $departments = Department::all();
        return view('admin.lecturers.edit', compact('lecturer', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $lecturer = Lecturer::where('id', $id)->with(['user' => function ($q) {
            $q->withTrashed();
        }])->firstOrFail();

        $user = $lecturer->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'lecturer_code' => 'required|string|unique:lecturers,lecturer_code,' . $lecturer->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $avatarPath = $user->avatar_url;
            if ($request->hasFile('avatar')) {
                if ($user->avatar_url) {
                    Storage::disk('public')->delete($user->avatar_url);
                }
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update([
                'name' => trim($request->name),
                'email' => $request->email,
                'phone' => $request->phone,
                'username' => $request->lecturer_code,
                'avatar_url' => $avatarPath,
            ]);

            $lecturer->update([
                'department_id' => $request->department_id,
                'lecturer_code' => $request->lecturer_code,
                'degree' => $request->degree,
                'position' => $request->position,
            ]);

            DB::commit();

            return redirect()->route('admin.lecturers.index')
                ->with('success', 'Cập nhật thông tin thành công!')
                ->with('highlight_id', $lecturer->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // 1. Ẩn 1 dòng (Soft Delete)
    public function destroy($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        if (\App\Models\Classes::where('advisor_id', $id)->exists()) {
            return back()->with('error', 'Không thể ẩn! Giảng viên đang là Cố vấn học tập.');
        }
        $lecturer->user->delete();
        return redirect()->back()->with('success', 'Đã ẩn giảng viên (Chuyển sang trạng thái vô hiệu hóa).');
    }

    // 2. Khôi phục 1 dòng
    public function restore($id)
    {
        $lecturer = Lecturer::where('id', $id)->with(['user' => function ($q) {
            $q->withTrashed();
        }])->firstOrFail();

        if ($lecturer->user->trashed()) {
            $lecturer->user->restore();
        }
        return redirect()->back()->with('success', 'Đã khôi phục hoạt động cho giảng viên.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) return response()->json(['error' => 'Chưa chọn mục nào.'], 400);

        // Chỉ lấy những cái chưa xóa để xóa
        $lecturers = Lecturer::whereIn('id', $ids)->get();
        $count = 0;
        foreach ($lecturers as $lec) {
            // Logic xóa mềm User
            if ($lec->user && !$lec->user->trashed()) {
                if (\App\Models\Classes::where('advisor_id', $lec->id)->exists()) continue;
                $lec->user->delete();
                $count++;
            }
        }
        return response()->json(['success' => true, 'message' => "Đã ẩn $count giảng viên."]);
    }

    // 4. Khôi phục hàng loạt
    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) return response()->json(['error' => 'Chưa chọn mục nào.'], 400);

        // Lấy cả những cái đã xóa để khôi phục
        $lecturers = Lecturer::whereIn('id', $ids)->with(['user' => function ($q) {
            $q->withTrashed();
        }])->get();

        $count = 0;
        foreach ($lecturers as $lec) {
            if ($lec->user && $lec->user->trashed()) {
                $lec->user->restore();
                $count++;
            }
        }
        return response()->json(['success' => true, 'message' => "Đã khôi phục $count giảng viên."]);
    }

    private function vn_to_str($str)
    {
        $str = $str ?? '';
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
        return strtolower(str_replace(' ', '', $str));
    }
}
