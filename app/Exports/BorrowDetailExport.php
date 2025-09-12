<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

use App\Models\Borrow;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowDetailExport {
    protected $templateFile = '';
    public function rules() : array{
        $rules = [
            'id' => 'required|exists:borrows,id'
        ];
        return $rules;
    }
    public $messages = [
        'required' => 'Trường yêu cầu',
        'exists' => 'ID không tồn tại',
    ];
    public function handle($request = null){
        
        $id = request()->id;
        $type = request()->type;
        // Lấy thông tin người dùng và mượn thiết bị
        $borrow = Borrow::find($id);
        $user = $borrow->user;

        // Đường dẫn đến mẫu Excel đã có sẵn
        $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');

        // Tạo một Spreadsheet từ mẫu
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($templatePath);

        // Lấy ngày, tháng và năm hiện tại
        $currentDay = date('d',strtotime($borrow->created_at));
        $currentMonth = date('m',strtotime($borrow->created_at));
        $currentYear = date('Y',strtotime($borrow->created_at));
        
        // Lấy đơn vị tạo
        $company_parent = \App\Models\Option::get_option('general','company_parent');
        $company_name   = \App\Models\Option::get_option('general','company_name');
        $company_address   = \App\Models\Option::get_option('general','company_address');
        $newValue = $company_address.', ngày ' . $currentDay . ' tháng ' . $currentMonth . ' năm ' . $currentYear;
        
        // Lấy sheet hiện tại
        $sheet = $spreadsheet->getActiveSheet();
        // Tên sở
        $sheet->setCellValue('A1', $company_parent ?? '');
        // Tên trường 
        $sheet->setCellValue('A2', $company_name ?? '');

        // Họ và tên
        $sheet->setCellValue('B6', $user->name ?? '');
        // Ngày dạy
        $sheet->setCellValue('B7', date('d/m/Y',strtotime($borrow->borrow_date)));
        // Tổ
        $sheet->setCellValue('B8', $user->nest->name ?? '');
        //Mã phiếu:
        $sheet->setCellValue('G6', $id);
        //Ngày tạo:
        $sheet->setCellValue('G7', date('d/m/Y',strtotime($borrow->created_at)));

        // Lấy style mẫu từ hàng 11 (A11)
        $styleMau = $sheet->getStyle('A11');

        // Duyệt qua danh sách mượn thiết bị
        $index = 11; // Bắt đầu từ hàng 11
        $stt   = 1;  // Khởi tạo biến STT
        foreach ($borrow->the_devices as $device) {
            $sheet->setCellValue('A' . $index, $stt);
            $sheet->setCellValue('B' . $index, $device->device->name ?? '');
            $sheet->setCellValue('C' . $index, $device->lesson_name);
            $sheet->setCellValue('D' . $index, \Carbon\Carbon::parse($borrow->borrow_date)->format('d/m/Y'));
            $sheet->setCellValue('E' . $index, $device->lecture_name);
            $sheet->setCellValue('F' . $index, $device->quantity);
            $sheet->setCellValue('G' . $index, $device->session == 'Chiều' ? 'C:'.$device->lecture_number : 'S:'.$device->lecture_number);
            $sheet->setCellValue('H' . $index, $device->room->name ?? '');
            // Copy style từ A11 cho cả dòng mới
            for ($col = 'A'; $col <= 'H'; $col++) {
                $sheet->duplicateStyle($styleMau, $col . $index);
            }
            $index++;
            $stt++;
        }


        $spreadsheet->setActiveSheetIndex(0);
        $newFilePath = public_path('system/tmp/'.strtolower($type).'-'.$borrow->id.'-'.date('d-m-Y-H-i-s').'.xlsx');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($newFilePath);
        return $newFilePath;
        
    }
}