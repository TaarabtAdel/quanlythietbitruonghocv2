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

use App\Models\InventoryAudit;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryAuditExport {
    protected $templateFile = '';
    public function rules() : array{
        $rules = [
            'id' => 'required|exists:inventory_audits,id'
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
        $inventory_audit = InventoryAudit::find($id);
        $user = $inventory_audit->user;

        // Đường dẫn đến mẫu Excel đã có sẵn
        $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');

        // Tạo một Spreadsheet từ mẫu
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($templatePath);

        // Lấy ngày, tháng và năm hiện tại
        $currentDay = date('d',strtotime($inventory_audit->created_at));
        $currentMonth = date('m',strtotime($inventory_audit->created_at));
        $currentYear = date('Y',strtotime($inventory_audit->created_at));
        
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

        // Tên
        $sheet->setCellValue('C6', $inventory_audit->name ?? '');
        //Ngày kiểm:
        $sheet->setCellValue('C7', date('d/m/Y',strtotime($inventory_audit->audit_date)));

        //Mã phiếu:
        $sheet->setCellValue('J7', $id);

        //Năm học:
        $sheet->setCellValue('J6', $inventory_audit->school_year);
        

        // Lấy style mẫu từ hàng 12
        $styleArrayA12 = $sheet->getStyle('A12')->exportArray();

        // Copy chiều cao dòng
        $rowHeight = $sheet->getRowDimension(12)->getRowHeight();

        // Tìm merge của dòng 12 (ví dụ B12:C12)
        $mergeCols = [];
        foreach ($sheet->getMergeCells() as $merge) {
            if (preg_match('/([A-Z]+)12:([A-Z]+)12/', $merge, $m)) {
                $mergeCols[] = [$m[1], $m[2]];  // ví dụ ['B', 'C']
            }
        }

        $index = 12;
        $stt   = 1;

        foreach ($inventory_audit->records as $record) {

            //--- Gán dữ liệu ---
            $sheet->setCellValue('A' . $index, $stt);
            $sheet->setCellValue('B' . $index, $record->device->name ?? '');
            $sheet->setCellValue('D' . $index, $record->device->year ?? '');
            $sheet->setCellValue('E' . $index, $record->device->country_name ?? '');
            $sheet->setCellValue('F' . $index, $record->device->unit);
            $sheet->setCellValue('G' . $index, $record->device->price);
            $sheet->setCellValue('H' . $index, $record->initial_total ?? 0);
            $sheet->setCellValue('I' . $index, $record->initial_damaged ?? 0);
            $sheet->setCellValue('J' . $index, $record->increase_quantity ?? 0);
            $sheet->setCellValue('K' . $index, $record->decrease_quantity ?? 0);
            $sheet->setCellValue('L' . $index, $record->final_total ?? 0);
            $sheet->setCellValue('M' . $index, $record->final_damaged ?? 0);

            // --- Copy merge của dòng 12 ---
            foreach ($mergeCols as [$colStart, $colEnd]) {
                $newMerge = "{$colStart}{$index}:{$colEnd}{$index}";
                $sheet->mergeCells($newMerge);

                // --- Set border cho vùng merge cùng lúc ---
                $sheet->getStyle($newMerge)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => ($colStart === 'B') ? \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT : \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ]
                ]);
            }

            //--- Sao chép style FULL từ A12 sang A$index..M$index ---
            for ($col = 'A'; $col <= 'M'; $col++) {
                // Nếu ô này thuộc merge và không phải ô đầu → bỏ qua
                if ($sheet->getCell($col . '12')->isInMergeRange()) {
                    if (!$sheet->getCell($col . '12')->isMergeRangeValueCell()) {
                        continue;
                    }
                }
                $sheet->getStyle($col . $index)->applyFromArray($styleArrayA12);
            }

            $sheet->getStyle('B'.$index)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

            // Copy chiều cao dòng
            $sheet->getRowDimension($index)->setRowHeight($rowHeight);

            $index++;
            $stt++;
        }


        // Lưu file
        $spreadsheet->setActiveSheetIndex(0);
        $newFilePath = public_path('system/tmp/'.strtolower($type).'-'.$inventory_audit->id.'-'.date('d-m-Y-H-i-s').'.xlsx');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($newFilePath);

        return $newFilePath;


        
    }
}