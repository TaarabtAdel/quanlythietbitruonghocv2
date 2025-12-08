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
        
        // 1. Lấy dữ liệu và kiểm tra
        $inventory_audit = InventoryAudit::find($id);
        if (!$inventory_audit) {
            return "Không tìm thấy phiếu kiểm kê với ID: " . $id;
        }

        $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');
        if (!file_exists($templatePath)) {
            return "Không tìm thấy file mẫu Excel tại đường dẫn: " . $templatePath;
        }

        // 2. Tải Spreadsheet và Lấy thông tin chung
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($templatePath);
        
        $commonData = $this->getCommonData($inventory_audit);

        // 3. Xử lý từng Sheet
        // Xử lý Sheet 1 (Index 0)
        $this->fillSheet1($spreadsheet->getSheet(0), $inventory_audit, $commonData);
        
        // Xử lý Sheet 2 (Index 1) - Nếu tồn tại
        if ($spreadsheet->getSheetCount() > 1) {
            $this->fillSheet2($spreadsheet->getSheet(1), $inventory_audit, $commonData);
        }

        // 4. Lưu file
        $spreadsheet->setActiveSheetIndex(0);
        $newFilePath = public_path('system/tmp/'.strtolower($type).'-'.$inventory_audit->id.'-'.date('d-m-Y-H-i-s').'.xlsx');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($newFilePath);

        return $newFilePath;
    }

    private function getCommonData($inventory_audit) {
        $currentDay = date('d',strtotime($inventory_audit->created_at));
        $currentMonth = date('m',strtotime($inventory_audit->created_at));
        $currentYear = date('Y',strtotime($inventory_audit->created_at));
        
        return [
            // Giả định Option::get_option() hoạt động tốt
            'company_parent'    => \App\Models\Option::get_option('general','company_parent'),
            'company_name'      => \App\Models\Option::get_option('general','company_name'),
            'company_address'   => \App\Models\Option::get_option('general','company_address'),
            'id'                => $inventory_audit->id,
            'audit_date'        => date('d/m/Y',strtotime($inventory_audit->audit_date)),
            'school_year'       => $inventory_audit->school_year,
            'name'              => $inventory_audit->name,
            'date_string'       => \App\Models\Option::get_option('general','company_address') 
                                    . ', ngày ' . $currentDay . ' tháng ' . $currentMonth 
                                    . ' năm ' . $currentYear,
        ];
    }

    private function fillSheet1(Worksheet $sheet, $inventory_audit, $data)
    {
        // 1. Điền Dữ Liệu Cố Định
        $sheet->setCellValue('A1', $data['company_parent'] ?? '');
        $sheet->setCellValue('A2', $data['company_name'] ?? '');
        $sheet->setCellValue('C6', $data['name'] ?? '');
        $sheet->setCellValue('C7', $data['audit_date']);
        $sheet->setCellValue('J7', $data['id']);
        $sheet->setCellValue('J6', $data['school_year']);

        // 2. Xử lý Danh Sách Thiết Bị ( Sheet Sổ kiểm kê )
        $this->fillDeviceRecords($sheet, $inventory_audit->records);
    }

    private function fillSheet2(Worksheet $sheet, $inventory_audit, $data)
    {
        // 1. Điền Dữ Liệu Cố Định (Hiện tại đang giống Sheet 1, bạn có thể TÙY CHỈNH TẠI ĐÂY)
        $sheet->setCellValue('A1', $data['company_parent'] ?? '');
        $sheet->setCellValue('A2', $data['company_name'] ?? '');
        $sheet->setCellValue('C6', $data['name'] ?? '');
        $sheet->setCellValue('C7', $data['audit_date']);
        $sheet->setCellValue('J7', $data['id']);
        $sheet->setCellValue('J6', $data['school_year']);
        
        // 2. Xử lý Danh Sách Thiết Bị (Sheet Phiếu báo cáo)
        $this->fillDeviceRecordsReport($sheet, $inventory_audit->records);
    }

    private function fillDeviceRecords(Worksheet $sheet, $records)
    {
        // Lấy style mẫu từ hàng 12
        $styleArrayA12 = $sheet->getStyle('A12')->exportArray();
        $rowHeight = $sheet->getRowDimension(12)->getRowHeight();
        
        // Tìm merge của dòng 12
        $mergeCols = [];
        foreach ($sheet->getMergeCells() as $merge) {
            if (preg_match('/([A-Z]+)12:([A-Z]+)12/', $merge, $m)) {
                $mergeCols[] = [$m[1], $m[2]]; 
            }
        }
        $index = 12;
        $stt = 1;
        
        foreach ($records as $record) {
            
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

            // --- Copy merge, border và alignment ---
            foreach ($mergeCols as [$colStart, $colEnd]) {
                $newMerge = "{$colStart}{$index}:{$colEnd}{$index}";
                $sheet->mergeCells($newMerge);
                
                $sheet->getStyle($newMerge)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => ($colStart === 'B') ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ]
                ]);
            }
            
            //--- Sao chép style FULL từ A12 sang A$index..M$index ---
            for ($col = 'A'; $col <= 'M'; $col++) {
                // Nếu ô này thuộc merge và không phải ô đầu → bỏ qua
                $originalCell = $sheet->getCell($col . '12');
                if ($originalCell->isInMergeRange() && !$originalCell->isMergeRangeValueCell()) {
                     continue;
                }
                $sheet->getStyle($col . $index)->applyFromArray($styleArrayA12);
            }
            
            // Thiết lập alignment riêng cho cột B (Tên thiết bị)
            $sheet->getStyle('B'.$index)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

            // Copy chiều cao dòng
            $sheet->getRowDimension($index)->setRowHeight($rowHeight);
            
            $index++;
            $stt++;
        }
    }

    private function fillDeviceRecordsReport(Worksheet $sheet, $records){
        // Lấy style mẫu từ hàng 11
        $styleArrayA11 = $sheet->getStyle('A11')->exportArray();
        $rowHeight = $sheet->getRowDimension(11)->getRowHeight();
        
        // Tìm merge của dòng 11
        $mergeCols = [];
        foreach ($sheet->getMergeCells() as $merge) {
            if (preg_match('/([A-Z]+)11:([A-Z]+)11/', $merge, $m)) {
                $mergeCols[] = [$m[1], $m[2]]; 
            }
        }
        $index = 11;
        $stt = 1;

        foreach ($records as $record) {
            //--- Gán dữ liệu ---
            $sheet->setCellValue('A' . $index, $stt);
            // Tên thiết bị
            $sheet->setCellValue('B' . $index, $record->device->name ?? '');
            // Nước sx
            $sheet->setCellValue('D' . $index, $record->device->country_name ?? '');
            // Năm sx
            $sheet->setCellValue('E' . $index, $record->device->year ?? '');
            // F: Số lượng
            $sheet->setCellValue('F' . $index, $record->initial_total .' '. $record->device->unit);
            // G: Đơn giá
            $sheet->setCellValue('G' . $index, $record->device->price);
            // H: Thành tiền
            $sheet->setCellValue('H' . $index, (int)$record->device->price * $record->initial_total);
            // I: Hỏng
            $sheet->setCellValue('I' . $index, $record->initial_damaged ?? 0);
            // J: Số lượng (Hiện có)
            $sheet->setCellValue('J' . $index, $record->final_total ?? 0);
            // K: Đơn giá (Hiện có)
            $sheet->setCellValue('K' . $index, $record->device->price ?? 0);
            // L: Thành tiền (Hiện có)
            $sheet->setCellValue('L' . $index, (int)$record->device->price * $record->final_total);

            // --- Copy merge, border và alignment ---
            foreach ($mergeCols as [$colStart, $colEnd]) {
                $newMerge = "{$colStart}{$index}:{$colEnd}{$index}";
                $sheet->mergeCells($newMerge);
                $sheet->getStyle($newMerge)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => ($colStart === 'B') ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ]
                ]);
            }
            //--- Sao chép style FULL từ A11 sang A$index..M$index ---
            for ($col = 'A'; $col <= 'L'; $col++) {
                // Nếu ô này thuộc merge và không phải ô đầu → bỏ qua
                $originalCell = $sheet->getCell($col . '11');
                if ($originalCell->isInMergeRange() && !$originalCell->isMergeRangeValueCell()) {
                     continue;
                }
                $sheet->getStyle($col . $index)->applyFromArray($styleArrayA11);
            }
            
            // Thiết lập alignment riêng cho cột B (Tên thiết bị)
            $sheet->getStyle('B'.$index)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

            // Copy chiều cao dòng
            $sheet->getRowDimension($index)->setRowHeight($rowHeight);
            
            $index++;
            $stt++;
        
        }

    }
}