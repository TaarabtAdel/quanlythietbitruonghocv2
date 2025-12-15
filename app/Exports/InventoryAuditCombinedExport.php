<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

use App\Models\InventoryAudit;
use App\Models\InventoryRecord;
use App\Models\Option;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection; // Để sử dụng Collection

class InventoryAuditCombinedExport {
    protected $templateFile = '';
    
    public function rules() : array{
        $rules = [
            'origin_year' => 'required'
        ];
        return $rules;
    }
    
    public $messages = [
        'required' => 'Trường yêu cầu',
    ];

    // --- Hàm điều khiển chính ---
    
    public function handle($request = null){
        
        $type = request()->type;
        $origin_year = request()->origin_year;
        
        // 1. Thu thập danh sách các năm cần xuất
        $additionalYears = [
            'year_2' => request()->year_2,
            'year_3' => request()->year_3,
            'year_4' => request()->year_4,
            'year_5' => request()->year_5,
        ];
        $yearsToExport = array_filter($additionalYears); // Chỉ lấy các năm không rỗng

        // 2. Lấy dữ liệu năm gốc và kiểm tra
        $inventory_audit_origin = InventoryAudit::where('id', $origin_year)->first();
        if (!$inventory_audit_origin) {
            return "Không tìm thấy phiếu kiểm kê cho năm học gốc: " . $origin_year;
        }

        $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');
        if (!file_exists($templatePath)) {
            return "Không tìm thấy file mẫu Excel tại đường dẫn: " . $templatePath;
        }

        // 3. Tải Spreadsheet và Lấy thông tin chung
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($templatePath);
        $sheet = $spreadsheet->getSheet(0); // Chỉ làm việc với Sheet 1
        
        $commonData = $this->getCommonData($inventory_audit_origin);

        // 4. Thu thập tất cả các năm học cần xem xét (cả năm gốc và năm phụ)
        $allYearsToQuery = $yearsToExport;
        $allYearsToQuery['origin'] = $origin_year; // Thêm năm gốc vào danh sách truy vấn

        // 5. Lấy TẤT CẢ records từ TẤT CẢ các năm và ánh xạ
        $allRecordsMapped = $this->getAllDeviceRecords($allYearsToQuery);
        
        $unionDevices = $allRecordsMapped['union_devices']; 
        $allAuditData = $allRecordsMapped['all_audit_data']; 
        
        // 6. Điền dữ liệu
        $this->fillSheet(
            $sheet, 
            $inventory_audit_origin, 
            $commonData, 
            $unionDevices, // Danh sách Union
            $allAuditData, // Tất cả dữ liệu Audit
            $origin_year, // ID Năm gốc
            $yearsToExport // Các năm phụ
        );

        // 7. Lưu file
        $spreadsheet->setActiveSheetIndex(0);
        $newFilePath = public_path('system/tmp/'.strtolower($type).'-'.date('d-m-Y-H-i-s').'.xlsx');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($newFilePath);

        return $newFilePath;
    }

    // --- Hàm phụ trợ: Lấy dữ liệu chung ---
    
    private function getCommonData($inventory_audit) {
        $currentDay = date('d',strtotime($inventory_audit->created_at));
        $currentMonth = date('m',strtotime($inventory_audit->created_at));
        $currentYear = date('Y',strtotime($inventory_audit->created_at));
        
        // Giả định Option::get_option() hoạt động tốt
        return [
            'company_parent'    => \App\Models\Option::get_option('general','company_parent'),
            'company_name'      => \App\Models\Option::get_option('general','company_name'),
            'id'                => $inventory_audit->id,
            'audit_date'        => date('d/m/Y',strtotime($inventory_audit->audit_date)),
            'school_year'       => $inventory_audit->school_year,
            'name'              => $inventory_audit->name,
        ];
    }
    
    /**
     * Lấy TẤT CẢ records từ TẤT CẢ các năm, ánh xạ theo device_id và school_year, 
     * đồng thời trả về danh sách Device đầy đủ.
     */
    private function getAllDeviceRecords(array $allYearsToQuery) 
    {
        // Lấy InventoryAudit IDs của tất cả các năm học cần xuất
        $auditIds = InventoryAudit::whereIn('id', $allYearsToQuery)
                                  ->pluck('id', 'school_year')
                                  ->toArray();

        if (empty($auditIds)) {
            return ['union_devices' => collect(), 'all_audit_data' => []];
        }

        $auditIdList = array_values($auditIds);

        // Lấy tất cả InventoryRecord của các phiếu Audit tìm được, và Eager Load Device
        $records = InventoryRecord::whereIn('inventory_audit_id', $auditIdList)
                                  ->with(['inventoryAudit', 'device']) 
                                  ->get();

        $allAuditData = [];
        $allDeviceIds = [];

        foreach ($records as $record) {
            $schoolYear = $record->inventoryAudit->school_year;
            $deviceId = $record->device_id;
            
            // Lưu thông tin Device duy nhất (sử dụng device_id làm key)
            $allDeviceIds[$deviceId] = $record->device; 
            
            // Lưu dữ liệu kiểm kê (4 trường + 2 trường initial)
            $allAuditData[$deviceId][$schoolYear] = [
                'increase_quantity' => $record->increase_quantity ?? 0,
                'decrease_quantity' => $record->decrease_quantity ?? 0,
                'final_total'       => $record->final_total ?? 0,
                'final_damaged'     => $record->final_damaged ?? 0,
                'initial_total'     => $record->initial_total ?? 0,
                'initial_damaged'   => $record->initial_damaged ?? 0,
            ];
        }

        // $allDeviceIds là danh sách các model Device duy nhất, sắp xếp theo tên
        return [
            'union_devices' => collect($allDeviceIds)->sortBy('name'), 
            'all_audit_data' => $allAuditData
        ];
    }
    
    // --- Hàm xử lý điền dữ liệu cho Sheet duy nhất ---

    private function fillSheet(Worksheet $sheet, $inventory_audit_origin, $data, $unionDevices, array $allAuditData, $originYear, array $yearsToExport)
    {
        $originYear = InventoryAudit::find($originYear)->school_year;
        // 1. Điền Dữ Liệu Cố Định
        $sheet->setCellValue('A1', $data['company_parent'] ?? '');
        $sheet->setCellValue('A2', $data['company_name'] ?? '');
        $sheet->setCellValue('C6', $data['name'] ?? '');
        $sheet->setCellValue('C7', $data['audit_date']);
        $sheet->setCellValue('J7', $data['id']);
        $sheet->setCellValue('J6', $data['school_year']); // Năm gốc

        // --- 2. Cập nhật Tiêu đề Năm học và Tiêu đề Cột Biến Động ---
        
        // a) Tiêu đề Biến động cho Năm Gốc (J9)
        // Yêu cầu: J9:M9 đã được merge trong file mẫu
        $sheet->setCellValue('J9', 'BIẾN ĐỘNG TRONG NĂM ' . $originYear);
        
        // b) Tiêu đề Năm học chính (Hàng 11) và Tiêu đề Biến động (Hàng 9) cho Các Năm Phụ
        $startColIndex = 14; // Bắt đầu từ cột N
        $colIndex = $startColIndex;
        
        // Cột bắt đầu tiêu đề biến động cho năm phụ: N, R, V, Z (tức là 14, 18, 22, 26)
        $colStartTitles = [14, 18, 22, 26]; 
        $titleIndex = 0;

        foreach ($yearsToExport as $year) {
            $year = InventoryAudit::find($year)->school_year;
            $colName11 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            
            // 1. Thiết lập tiêu đề năm học chính (Hàng 11)
            $sheet->setCellValue($colName11 . '11', $year); 
            
            // 2. Thiết lập tiêu đề "BIẾN ĐỘNG TRONG NĂM" (Hàng 9)
            if (isset($colStartTitles[$titleIndex])) {
                $colName9 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colStartTitles[$titleIndex]);
                
                // Set tiêu đề "BIẾN ĐỘNG TRONG NĂM"
                // Yêu cầu: N9:Q9, R9:U9, V9:Y9, Z9:CC9 đã được merge trong file mẫu
                $sheet->setCellValue($colName9 . '9', 'BIẾN ĐỘNG TRONG NĂM ' . $year);
            }
            
            $colIndex += 4; // Bốn cột cho mỗi năm
            $titleIndex++;
        }

        // 3. Xử lý Danh Sách Thiết Bị
        $this->fillDeviceRecords(
            $sheet, 
            $unionDevices, 
            $allAuditData, 
            $originYear, 
            $yearsToExport
        );
    }

    /**
     * Điền danh sách records (dựa trên UnionDevices) và dữ liệu của tất cả các năm học.
     */
    private function fillDeviceRecords(Worksheet $sheet, $unionDevices, array $allAuditData, $originYear, array $yearsToExport)
    {
        // Lấy style mẫu từ hàng 12
        $styleArrayA12 = $sheet->getStyle('A12')->exportArray();
        $rowHeight = $sheet->getRowDimension(12)->getRowHeight();
        
        // Tìm merge của dòng 12 (ví dụ B12:C12)
        $mergeCols = [];
        foreach ($sheet->getMergeCells() as $merge) {
            if (preg_match('/([A-Z]+)12:([A-Z]+)12/', $merge, $m)) {
                $mergeCols[] = [$m[1], $m[2]]; 
            }
        }
        
        // Tính toán cột cuối cùng để sao chép style và border
        $endColIndex = 13 + (count($yearsToExport) * 4);
        $endColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endColIndex);

        $index = 12;
        $stt = 1;
        
        // Lặp qua danh sách Device tổng hợp
        foreach ($unionDevices as $device) { 
            $deviceId = $device->id;
            
            // Lấy records (năm gốc) từ dữ liệu tổng hợp
            $originData = $allAuditData[$deviceId][$originYear] ?? [];
            
            // --- Gán dữ liệu cơ bản (A đến I) ---
            $sheet->setCellValue('A' . $index, $stt);
            $sheet->setCellValue('B' . $index, $device->name ?? '');
            $sheet->setCellValue('D' . $index, $device->year ?? '');
            $sheet->setCellValue('E' . $index, $device->country_name ?? '');
            $sheet->setCellValue('F' . $index, $device->unit);
            $sheet->setCellValue('G' . $index, $device->price);
            
            // Dữ liệu ban đầu (H, I)
            $sheet->setCellValue('H' . $index, $originData['initial_total'] ?? 0); 
            $sheet->setCellValue('I' . $index, $originData['initial_damaged'] ?? 0); 
            
            // Dữ liệu kiểm kê gốc (J, K, L, M)
            $sheet->setCellValue('J' . $index, $originData['increase_quantity'] ?? 0);
            $sheet->setCellValue('K' . $index, $originData['decrease_quantity'] ?? 0);
            $sheet->setCellValue('L' . $index, $originData['final_total'] ?? 0);
            $sheet->setCellValue('M' . $index, $originData['final_damaged'] ?? 0);
            
            // --- Gán dữ liệu Các Năm Phụ (Bắt đầu từ cột N) ---
            $currentColIndex = 14; 
            foreach ($yearsToExport as $schoolYear) {
                $schoolYear = InventoryAudit::find($schoolYear)->school_year;
                // Lấy dữ liệu của năm học này cho thiết bị hiện tại (sử dụng ?? [] để điền 0 nếu không có)
                $yearData = $allAuditData[$deviceId][$schoolYear] ?? [];
                
                // Gán 4 giá trị vào 4 cột liên tiếp
                $colJ = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex);
                $colK = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex + 1);
                $colL = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex + 2);
                $colM = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex + 3);
                
                $sheet->setCellValue($colJ . $index, $yearData['increase_quantity'] ?? 0);
                $sheet->setCellValue($colK . $index, $yearData['decrease_quantity'] ?? 0);
                $sheet->setCellValue($colL . $index, $yearData['final_total'] ?? 0);
                $sheet->setCellValue($colM . $index, $yearData['final_damaged'] ?? 0);

                $currentColIndex += 4; 
            }


            // --- Copy merge (Chỉ áp dụng cho các cột A-M ban đầu) ---
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
            
            //--- Sao chép style và áp dụng Border cho tất cả các cột đến cột cuối cùng ($endColLetter) ---
            for ($col = 'A'; $col <= $endColLetter; $col++) {
                
                // 1. Sao chép style cho các cột A-M (loại trừ các ô merge phụ)
                if (\PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col) <= 13) {
                    $originalCell = $sheet->getCell($col . '12');
                    if ($originalCell->isInMergeRange() && !$originalCell->isMergeRangeValueCell()) {
                         continue;
                    }
                    $sheet->getStyle($col . $index)->applyFromArray($styleArrayA12);
                }
                
                // 2. ÁP DỤNG BORDER CHUNG cho TẤT CẢ các cột
                $sheet->getStyle($col . $index)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => ($col === 'B') ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ]
                ]);
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