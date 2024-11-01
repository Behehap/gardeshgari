<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255|unique:articles,title', // عنوان یونیک و حداکثر ۲۵۵ کاراکتر
            'content' => 'required', // محتوای ارسال‌شده به‌صورت فایل (محدود به فرمت‌های خاص و اندازه)
            'categories' => 'nullable|array', // آرایه‌ای از دسته‌بندی‌ها
            'categories.*' => 'exists:categories,id', // بررسی اینکه هر دسته‌بندی در جدول categories موجود باشد
            'img' => 'required|image|mimes:jpeg,png,jpg|max:2048', // تصویر اجباری با فرمت و حجم مشخص
        ];
    }
}
