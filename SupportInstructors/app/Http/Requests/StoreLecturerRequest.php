<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLecturerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Quan trọng: unique:users,email và unique:lecturers,lecturer_code
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
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống. Vui lòng nhập email khác.',
            'lecturer_code.required' => 'Vui lòng nhập mã giảng viên.',
            'lecturer_code.unique' => 'Mã giảng viên này đã tồn tại trong hệ thống.',
            'department_id.required' => 'Vui lòng chọn khoa/đơn vị.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
            'avatar.max' => 'Dung lượng ảnh không được vượt quá 2MB.',
        ];
    }
}
