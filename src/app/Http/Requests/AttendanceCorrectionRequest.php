<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'clock_in_at' => ['required', 'date_format:H:i','before:clock_out_at'],
            'clock_out_at' => ['required', 'date_format:H:i','after:clock_in_at'],
            'break_start_at.*' => ['nullable', 'date_format:H:i','after:clock_in_at', 'before:clock_out_at'],
            'break_end_at.*' => ['nullable', 'date_format:H:i','after:break_start_at.*', 'before:clock_out_at'],
            'remarks' => ['required'],

            'new_break_start_at' => ['nullable', 'date_format:H:i', 'after:clock_in_at', 'before:clock_out_at'],
            'new_break_end_at' => ['nullable', 'date_format:H:i', 'after:new_break_start_at', 'before:clock_out_at'],

        ];
    }

    public function messages()
    {
        return [
            'clock_in_at.required' => '出勤時間を記入してください',
            'clock_out_at.required' => '退勤時間を記入してください',

            'clock_in_at.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out_at.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_start_at.*.after' => '休憩時間が不適切な値です',
            'break_start_at.*.before' => '休憩時間が不適切な値です',
            'break_end_at.*.before' => '休憩時間もしくは退勤時間が不適切な値です',
            'remarks.required' => '備考を記入してください',

            'new_break_start_at.after' => '休憩時間が不適切な値です',
            'new_break_start_at.before' => '休憩時間が不適切な値です',
            'new_break_end_at.before' => '休憩時間もしくは退勤時間が不適切な値です',

            'clock_in_at.date_format' => '正しい時間を入力してください',
            'clock_out_at.date_format' => '正しい時間を入力してください',
            'break_start_at.*.date_format' => '正しい時間を入力してください',
            'break_end_at.*.date_format' => '正しい時間を入力してください',

            'new_break_start_at.date_format' => '正しい時間を入力してください',
            'new_break_end_at.date_format' => '正しい時間を入力してください',
        ];
    }
}
