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

        // Duyệt qua danh sách mượn thiết bị
        $index = 2; // Bắt đầu từ hàng 10
        $stt = 1; // Khởi tạo biến STT
        foreach ($devices as $device) {
                $sheet->setCellValueExplicit('A' . $index, $stt, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getStyle('A' . $index)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL);
                $sheet->setCellValue('B' . $index, $device->name);
                $sheet->setCellValue('C' . $index, $device->country_name);
                $sheet->setCellValue('D' . $index, $device->year);
                $sheet->setCellValue('E' . $index, $device->quantity);
                $sheet->setCellValue('F' . $index, $device->unit);
                $sheet->setCellValue('G' . $index, $device->price);
                $sheet->setCellValue('H' . $index, $device->note);
                $sheet->setCellValue('I' . $index, $device->devicetype->name ?? '');
                $sheet->setCellValue('J' . $index, $device->department->name  ?? '');
                $sheet->setCellValue('K' . $index, "Thiết Bị");
                $index++;
                $stt++;
        }

        $spreadsheet->setActiveSheetIndex(0);
        $newFilePath = public_path('system/tmp/'.strtolower($type).'.xlsx');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($newFilePath);
        return $newFilePath;
    }
}