<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLecturerRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cho phép thực thi
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:15',
            'lecturer_code' => 'required|string|max:50|unique:lecturers,lecturer_code',
            'department_id' => 'required|exists:departments,id',
            'degree' => 'nullable|string',
            'position' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập họ tên giảng viên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'lecturer_code.required' => 'Vui lòng nhập mã giảng viên.',
            'lecturer_code.unique' => 'Mã giảng viên này đã tồn tại.',
            'department_id.required' => 'Vui lòng chọn khoa/đơn vị.',
            'department_id.exists' => 'Khoa/đơn vị không hợp lệ.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
            'avatar.mimes' => 'Chỉ chấp nhận định dạng: jpeg, png, jpg, gif.',
            'avatar.max' => 'Dung lượng ảnh không được vượt quá 2MB.',
        ];
    }
}
