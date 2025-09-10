<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Group;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Png;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\Borrow;

class ExportController extends Controller
{
    protected $view_path    = 'admin.export.';
    protected $route_prefix = 'admin.export.';

    public function index(Request $request)
    {
        $type = $request->type ?? '';
        if( !(auth()->user()->hasPermission('Export_'.$type))){
            abort(403);
        }
        $type_slug = Str::slug($type);
        $params = [
            'route_prefix'  => $this->route_prefix,
            'view_path'  => $this->view_path,
            'type_slug'  => $type_slug,
        ];
        return view($this->view_path.'index', $params);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = $request->type ?? '';
        $modelClass = '\App\Exports\\' . $type.'Export';
        $export = new $modelClass();
        $rules = $export->rules();
        $messages = $export->messages;
        if($rules){
            $validator = Validator::make($request->all(),$rules,$messages);
            if ($validator->fails()) {
                return redirect()
                            ->back()
                            ->with('error','Vui lòng nhập các trường bắt buộc')
                            ->withErrors($validator)
                            ->withInput();
            }
        }
        try {
            $newFilePath = $export->handle($request);
            return response()->download($newFilePath)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Xuất dữ liệu thất bại');
        }
    }

    public function preview(Request $request)
    {
        $type = $request->type ?? '';
        $params = $this->$type($request);
        return view($this->view_path.'preview.preview'.$type,compact('params'));
    }

    private function BorrowDetail ($data_request) {
        $borrow = Borrow::find($data_request->id);
        // Lấy ngày, tháng và năm hiện tại
        $currentDay = date('d');
        $currentMonth = date('m');
        $currentYear = date('Y');

       // Lấy đơn vị tạo
       $company_parent = \App\Models\Option::get_option('general','company_parent');
       $company_name   = \App\Models\Option::get_option('general','company_name');
       $company_address   = \App\Models\Option::get_option('general','company_address');
       $title = mb_strtoupper($company_parent.' '.$company_name,'UTF-8'); 
       $newValue = $company_address.', ngày ' . $currentDay . ' tháng ' . $currentMonth . ' năm ' . $currentYear;
       $params = [
           'currentYear' => $currentYear,
           'currentMonth' => $currentMonth,
           'currentDay' => $currentDay,
           'company_address' => $company_address,
           'newValue' => $newValue,
           'title' => $title,
           'data' => $borrow
       ];
        return $params;
    }

    private function BorrowDevicesNest ($data_request) {
        return 1;
    }
}