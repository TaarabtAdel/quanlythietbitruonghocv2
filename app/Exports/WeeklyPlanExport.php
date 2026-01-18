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

use App\Models\BorrowDevice;
use App\Models\Lab;
use App\Models\Borrow;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklyPlanExport {
    protected $templateFile = '';
    public function rules(): array
    {
        $rules = [
            'sw_start_week' => 'required',
        ];
        return $rules;
    }
    public $messages = [
        'required' => 'Trường là bắt buộc',
    ];
    public function handle($request = null){
        // $id = request()->id;
        $type = request()->type;
        
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
        if ($request->week) {
            $startDayEndDate = Borrow::getStartEndDateFromWeek($request->week);
        }
        if ($request->sw_start_week && $request->sw_end_week) {
            $startDayEndDate = [
                'startDate' => Carbon::parse($request->sw_start_week),
                'endDate'   => Carbon::parse($request->sw_end_week),
            ];
        }
        $startDay   = $startDayEndDate['startDate']->format('d/m/Y') ?? '';
        $endDay     = $startDayEndDate['endDate']->format('d/m/Y') ?? '';
        $sheet->setCellValue('C6',$startDay);
        $sheet->setCellValue('C7',$endDay);
        //Ngày xuất:
        $sheet->setCellValue('E7', date('d/m/Y'));

        $request = request();
        $items = BorrowDevice::getItems($request);

        // Duyệt qua danh sách mượn thiết bị
        $index = 10; // Bắt đầu từ hàng 10
        $stt = 1; // Khởi tạo biến STT
        foreach ($items as $item) {
            $session_name = $item['session'] == 'Sáng' ? 'S' : 'C';
            $device_name = $item['device_name'];
            $device_name = str_replace("<br>", "\n", $device_name);

            // Ngày dạy
            $sheet->setCellValue('A' . $index, $item['borrow_date']);
            // Giaos viên
            $sheet->setCellValue('B' . $index, $item['user_name']);
            // Thiet bị
            $sheet->setCellValue('C' . $index, $device_name);
            // Phong bo mon
            $sheet->setCellValue('E' . $index, $item['lab_name']);
            // Tiet day
            $sheet->setCellValue('F' . $index, $item['lecture_number'] . $session_name);
            // Lop
            $sheet->setCellValue('G' . $index, $item['room_name']);

            $sheet->getStyle('C' . $index)->getAlignment()->applyFromArray([
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ]);
            $lineCount = substr_count($device_name, "\n") + 1;
            $customHeight = $lineCount * 17; // Mỗi dòng ~20pt, cộng thêm 10pt đệm
            $sheet->getRowDimension($index)->setRowHeight($customHeight);

            // Copy style từ A11 cho cả dòng mới
            for ($col = 'A'; $col <= 'A'; $col++) { 
                $sheet->duplicateStyle($styleMau, $col . $index); 
            } 
            for ($colMerge = 'B'; $colMerge <= 'G'; $colMerge++) { 
                $sheet->duplicateStyle($styleMauMerge, $colMerge . $index); 
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