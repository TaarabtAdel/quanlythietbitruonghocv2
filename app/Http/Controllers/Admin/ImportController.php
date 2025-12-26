<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    protected $view_path    = 'admin.import.';
    protected $route_prefix = 'admin.import.';
    public function index(Request $request)
    {
        $type = $request->type ?? '';
        if( !(auth()->user()->hasPermission('Import_'.$type))){
            abort(403);
        }
        $type_slug = strtolower($type);
        $params = [
            'route_prefix'  => $this->route_prefix,
            'templateFile'  => $type_slug.'.xlsx',
        ];
        if($type == 'Curriculum'){
            return view($this->view_path.'types.'.$type_slug, $params);
        }
        return view($this->view_path.'index', $params);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = $request->type ?? '';
        $modelClass = '\App\Imports\\' . $type.'Import';
        
        // Truyền các tham số bổ sung cho CurriculumImport
        if($type == 'Curriculum') {
            $rules = [
                'file' => 'required|mimes:xlsx,xls',
                'school_year' => 'required|string',
                'department_id' => 'required|exists:departments,id',
            ];
            $messages = [
                'file.required' => 'Vui lòng chọn file',
                'file.mimes' => 'File phải có định dạng .xls hoặc .xlsx',
                'school_year.required' => 'Vui lòng chọn năm học',
                'department_id.required' => 'Vui lòng chọn bộ môn',
                'department_id.exists' => 'Bộ môn không tồn tại',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->with('error','Vui lòng nhập các trường bắt buộc')
                    ->withErrors($validator)
                    ->withInput();
            }
            
            $import = new $modelClass(
                $request->school_year,
                $request->department_id,
                $request->subject_type,
                $request->grade
            );
        } else {
            $import = new $modelClass();
            $rules = $import->rules ?? [];
            $messages = $import->messages ?? [];
            $rules = array_merge($rules,[
                'file' => 'required|mimes:xlsx, xls'
            ]);
            $messages = array_merge($messages,[
                'required' => 'Trường yêu cầu',
                'mimes' => 'Định dạng tệp không hỗ trợ',
            ]);
            if( count($rules) ){
                $validator = Validator::make($request->all(),$rules,$messages);
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->with('error','Vui lòng nhập các trường bắt buộc')
                        ->withErrors($validator)
                        ->withInput();
                }
            }
        }
        
        try {
            $file = $request->file('file');

            // lưu vào public/tmp
            $tmpPath = public_path('tmp');
            if (!file_exists($tmpPath)) {
                mkdir($tmpPath, 0777, true);
            }
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->move($tmpPath, $filename);

            // import từ path mới
            Excel::import($import, $path->getPathname());

            // xong thì xóa file tạm
            unlink($path->getPathname());

            return redirect()->back()->with('success', 'Nhập dữ liệu thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Nhập dữ liệu thất bại: ' . $e->getMessage());
        }
    }
}
