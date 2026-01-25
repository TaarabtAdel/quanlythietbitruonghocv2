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
            $device_name = str_replace("<br>", "\n", $item['device_name']);

            // 1. Gộp cột C và D
            $sheet->mergeCells("C{$index}:D{$index}");

            // 2. Đổ dữ liệu vào các ô
            $sheet->setCellValue('A' . $index, $item['borrow_date']);
            $sheet->setCellValue('B' . $index, $item['user_name']);
            $sheet->setCellValue('C' . $index, $device_name); // Dữ liệu cột C sẽ tràn sang D sau khi merge
            $sheet->setCellValue('E' . $index, $item['lab_name']);
            $sheet->setCellValue('F' . $index, $item['lecture_number'] . $session_name);
            $sheet->setCellValue('G' . $index, $item['room_name']);

            // 3. Sao chép Style từ dòng mẫu (A10:G10)
            $sheet->duplicateStyle($sheet->getStyle('A10:G10'), "A{$index}:G{$index}");

            // 4. Định dạng riêng cho vùng Merge C-D (Canh trái, xuống dòng, chiều cao)
            $sheet->getStyle("C{$index}:D{$index}")->getAlignment()->applyFromArray([
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ]);

            // Tính toán chiều cao dòng dựa trên nội dung cột C
            $lineCount = substr_count($device_name, "\n") + 1;
            $sheet->getRowDimension($index)->setRowHeight($lineCount * 13);

            // 5. FIX CỨNG BORDER: Đảm bảo toàn bộ dòng từ A đến G đều có khung, không mất cột G
            $sheet->getStyle("A{$index}:G{$index}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

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