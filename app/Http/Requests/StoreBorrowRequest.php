<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBorrowRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'borrow_date' => 'required',
            'devices.*.lesson_name' => 'required|max:254',
            'devices.*.lecture_name' => 'required|max:254',
        ];
        if( in_array(request()->task,['save-form','save-draft']) ){
            // Kiểm tra tiết dạy cần có thiết bị hoặc phòng bộ môn
            $validate_tiet = [];
            foreach( request()->devices as $tiet => $device ){
                $validate_tiet[$tiet] = true;
                $has_devices = false;
                $has_labs = false;
                if( !empty($device['device_id']) ){
                    $has_devices = true;
                }
                if( !empty($device['lab_id']) ){
                    $has_labs = true;
                }
                if( !$has_devices && !$has_labs ){
                    $rules['tiet_'.$tiet.'_validate'] = 'required';
                    $validate_tiet[$tiet] = false;
                }
            }

            // Kiểm tra tiết dạy cùng buổi và cùng tiết tkb
            $session_lectures = [];
            foreach( request()->devices as $tiet => $device ){
                $session_lectures[$tiet] = $device['session'].'-'.$device['lecture_number'];
            }

            if( count($session_lectures) ){
                $countValues = array_count_values($session_lectures);
                $duplicateValues = [];
                foreach ($countValues as $value => $count) {
                    if ($count > 1) {
                        $duplicateValues[] = $value;
                    }
                }
                $validate_session_lectures = [];
                foreach( $duplicateValues as $duplicateValue ){
                    foreach( $session_lectures as $tiet => $session_lecture ){
                        if( $duplicateValue == $session_lecture ){
                            $rules['tiet_'.$tiet.'_duplicate'] = 'required';
                        }   
                    }
                }
            }            
        }
        return $rules;
    }
    public function messages(): array
    {
        return [
            'borrow_date.required' => 'Trường yêu cầu',
            'devices.*.lesson_name.required' => 'Trường yêu cầu',
            'devices.*.lecture_name.required' => 'Trường yêu cầu',
            'tiet_*_validate.required' => 'Tiết dạy phải có thiết bị hoặc phòng bộ môn',
            'tiet_*_duplicate.required' => 'Tiết dạy này bị trùng buổi và tiết thời khóa biểu',
            'max' => 'Tối đa 255 kí tự'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'success' => false,
            'has_errors' => true,
        ], 200));
    }
}