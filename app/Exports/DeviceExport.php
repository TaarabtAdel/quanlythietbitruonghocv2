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

use App\Models\Device;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeviceExport {
    protected $templateFile = '';
    public function rules(): array
    {
        $rules = [
            
        ];
        return $rules;
    }

    public $messages = [
        
    ];
    public function handle($request = null){
        // $id = request()->id;
        $type = request()->type;
        
        $query = Device::query();
        
        $devices = $query->get();
        // Đường dẫn đến mẫu Excel đã có sẵn
        $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');

        // Tạo một Spreadsheet từ mẫu
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($templatePath);

        // Lấy sheet hiện tại
        $sheet = $spreadsheet->getActiveSheet();
        $styleMau = $sheet->getStyle('A10');

        // Lấy đơn vị tạo
        $company_parent = \App\Models\Option::get_option('general','company_parent');
        $company_name   = \App\Models\Option::get_option('general','company_name');
        $company_address   = \App\Models\Option::get_option('general','company_address');
        // Tên sở
        $sheet->setCellValue('A1', $company_parent ?? '');
        // Tên trường 
        $sheet->setCellValue('A2', $company_name ?? '');
        //Ngày xuất:
        $sheet->setCellValue('I6', date('d/m/Y'));

        // Duyệt qua danh sách mượn thiết bị
        $index = 10; // Bắt đầu từ hàng 10
        $stt = 1; // Khởi tạo biến STT
        foreach ($devices as $device) {
                $sheet->setCellValue('A' . $index, $stt);
                $sheet->setCellValue('B' . $index, $device->name);
                // $sheet->setCellValue('C' . $index, $device->country_name);
                $sheet->setCellValue('C' . $index, $device->year);
                $sheet->setCellValue('D' . $index, $device->quantity);
                $sheet->setCellValue('E' . $index, $device->unit);
                $sheet->setCellValue('F' . $index, $device->price);
                $sheet->setCellValue('G' . $index, $device->note);
                $sheet->setCellValue('H' . $index, $device->devicetype->name ?? '');
                $sheet->setCellValue('I' . $index, $device->department->name  ?? '');
                // Copy style từ A11 cho cả dòng mới
                for ($col = 'A'; $col <= 'I'; $col++) {
                    $sheet->duplicateStyle($styleMau, $col . $index);
                }
                $index++;
                $stt++;
        }

        $spreadsheet->setActiveSheetIndex(0);
        $newFilePath = public_path('system/tmp/'.strtolower($type).'-'.date('d-m-Y-H-i-s').'.xlsx');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($newFilePath);
        return $newFilePath;
    }
}