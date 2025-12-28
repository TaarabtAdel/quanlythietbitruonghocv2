<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Option;


use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $optionGroupNames = [
        'general' => 'Cấu hình chung',
        'borrow_device' => 'Mượn thiết bị',
        'borrow_lab' => 'Mượn phòng',
    ];
    protected $allOptions = [
        'general' => [
            [
                'option_name' => 'company_parent',
                'option_label' => 'Tên sở',
                'option_value' => '',
            ],
            [
                'option_name' => 'company_name',
                'option_label' => 'Tên trường',
                'option_value' => '',
            ],
            [
                'option_name' => 'company_email',
                'option_label' => 'Email',
                'option_value' => '',
            ],
            [
                'option_name' => 'company_phone',
                'option_label' => 'Số điện thoại',
                'option_value' => '',
            ],
            [
                'option_name' => 'company_address',
                'option_label' => 'Địa chỉ',
                'option_value' => '',
            ]
        ],
        'borrow_device' => [
            [
                'option_name' => 'auto_approved',
                'option_label' => 'Tự động <span class="fw-bold">Duyệt</span> phiếu khi Giáo Viên gửi yêu cầu',
                'option_value' => 1,
                'type' => 'checkbox'
            ],
            [
                'option_name' => 'allow_edit_approved',
                'option_label' => 'Cho phép Giáo Viên <span class="fw-bold text-uppercase">cập nhật</span> phiếu mượn sau khi phiếu <span class="fw-bold">Đã Duyệt</span>',
                'option_value' => 0,
                'type' => 'checkbox'
            ],
            [
                'option_name' => 'allow_edit_pending',
                'option_label' => 'Cho phép Giáo Viên <span class="fw-bold text-uppercase">cập nhật</span> phiếu mượn sau khi phiếu <span class="fw-bold">Chờ Duyệt</span>',
                'option_value' => 0,
                'type' => 'checkbox'
            ],
            [
                'option_name' => 'allow_delete_approved',
                'option_label' => 'Cho phép Giáo Viên <span class="fw-bold text-danger text-uppercase">xóa</span> phiếu mượn sau khi phiếu <span class="fw-bold">Đã Duyệt</span>',
                'option_value' => 0,
                'type' => 'checkbox'
            ],
            [
                'option_name' => 'allow_delete_pending',
                'option_label' => 'Cho phép Giáo Viên <span class="fw-bold text-danger text-uppercase">xóa</span> phiếu mượn sau khi phiếu <span class="fw-bold">Chờ Duyệt</span>',
                'option_value' => 0,
                'type' => 'checkbox'
            ],
            [
                'option_name' => 'check_duplicate',
                'option_label' => 'Kiểm tra trùng lặp thiết bị',
                'option_value' => 1,
                'type' => 'checkbox'
            ],
            [
                'option_name' => 'enable_fake_device',
                'option_label' => 'Cho phép giáo viên đưa lên thiết thị tự chuẩn bị trong phiếu mượn',
                'option_value' => 0,
                'type' => 'checkbox'
            ]
        ],
        'borrow_lab' => [
            [
                'option_name' => 'check_duplicate',
                'option_label' => 'Kiểm tra trùng lặp phòng',
                'option_value' => 1,
                'type' => 'checkbox'
            ]
        ]
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if( !(auth()->user()->hasPermission('System_Option'))){
            abort(403);
        }
        $optionGroupNames   = $this->optionGroupNames;
        $allOptions         = $this->allOptions;

        foreach( $allOptions as $option_group => $options ){
            foreach( $options as $key => $option_field ){
                $option_name = $option_field['option_name'];
                $option_value = Option::where('option_name',$option_name)
                ->where('option_group',$option_group)
                ->value('option_value');
                $allOptions[$option_group][$key]['option_value'] = $option_value;
            }
        }

        $params = [
            'optionGroupNames'  => $optionGroupNames,
            'allOptions'        => $allOptions
        ];
        return view('admin.options.index',$params);
    }
    public function update(Request $request)
    {
        $optionGroupNames   = $this->optionGroupNames;
        $allOptions         = $this->allOptions;

        $dataUpdate = $request->except('_token');
        foreach( $dataUpdate as $option_group => $options ){
            foreach( $options as $option_name => $value ){
                $updateoption = Option::where('option_name',$option_name)
                ->where('option_group',$option_group)->first();

                if($updateoption){
                    $updateoption->option_value = $value;
                    $updateoption->save();
                }else{
                    $option_label = $this->getOptionLabel($allOptions,$option_name);
                    Option::create([
                        'option_group' => $option_group,
                        'option_name' => $option_name,
                        'option_value' => $value,
                        'option_label' => $option_label,
                    ]);
                }

                
            }
        }
        return redirect()->back()->with('success','Cập nhật thành công !');
    }
    function getOptionLabel($array, $optionName) {
        foreach ($array as $category) {
            foreach ($category as $item) {
                if ($item['option_name'] === $optionName) {
                    return $item['option_label'];
                }
            }
        }
        return null; // Option name not found
    }

}