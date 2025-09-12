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

use App\Models\Nest;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NestExport {
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
        
        $query = Nest::orderBy('name','ASC');
        
        $nests = $query->get();
        // Đường dẫn đến mẫu Excel đã có sẵn
        $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');

        // Tạo một Spreadsheet từ mẫu
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($templatePath);

        // Lấy sheet hiện tại
        $sheet = $spreadsheet->getActiveSheet();
        $styleMau = $sheet->getStyle('A10');
        $styleMauMerge = $sheet->getStyle('A10');
        // Vùng merge mẫu
        $mergeRange = 'B10:E10';

        // Lấy đơn vị tạo
        $company_parent = \App\Models\Option::get_option('general','company_parent');
        $company_name   = \App\Models\Option::get_option('general','company_name');
        $company_address   = \App\Models\Option::get_option('general','company_address');
        // Tên sở
        $sheet->setCellValue('A1', $company_parent ?? '');
        // Tên trường 
        $sheet->setCellValue('A2', $company_name ?? '');
        //Ngày xuất:
        $sheet->setCellValue('E6', date('d/m/Y'));

        // Duyệt qua danh sách mượn thiết bị
        $index = 10; // Bắt đầu từ hàng 10
        $stt = 1; // Khởi tạo biến STT
        foreach ($nests as $nest) {
            $sheet->setCellValue('A' . $index, $stt);
            $sheet->setCellValue('B' . $index, $nest->name);
            // Copy style từ A11 cho cả dòng mới
            for ($col = 'A'; $col <= 'A'; $col++) { 
                $sheet->duplicateStyle($styleMau, $col . $index); 
            } 
            for ($colMerge = 'B'; $colMerge <= 'E'; $colMerge++) { 
                $sheet->duplicateStyle($styleMauMerge, $colMerge . $index); 
            }

            $newMergeRange = str_replace('10', $index, $mergeRange);
            $sheet->mergeCells($newMergeRange);

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