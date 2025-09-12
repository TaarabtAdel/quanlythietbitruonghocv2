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

use Carbon\Carbon;
use App\Models\Borrow;
use App\Models\User;

class BorrowDevicesNestExport
{
    public function rules(): array
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];
        return $rules;
    }

    public $messages = [
        'required' => 'Trường bắt buộc'
    ];
    
    public function handle()
    {
        $type = request()->type;
        // Xử lý tìm kiếm 
       $query = \App\Models\BorrowDevice::orderBy('borrow_date', 'asc')
        ->orderByRaw("CASE WHEN session = 'Sáng' THEN 1 WHEN session = 'Chiều' THEN 2 END")
        ->orderBy('lecture_number', 'asc')
        ->whereHas('borrow', function ($q) {
            $q->whereIn('status', [0, 1]); // cố định status 0,1
        });
        
        if(request()->nest_id){
            $query->whereHas('borrow.user', function ($query) {
                $query->where('nest_id', request()->nest_id );
            });
        }
        // Lấy thông tin theo thời gian
        $startDate = request()->start_date;
        $endDate = request()->end_date;
        $query->whereBetween('borrow_date', [$startDate, $endDate]);

        $borrowDevices = $query->get();
        $borrowDevices = \App\Models\BorrowDevice::groupBorrowDevices($borrowDevices);
        // Đường dẫn đến mẫu Excel đã có sẵn
        $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');

        // Tạo một Spreadsheet từ mẫu
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($templatePath);

        // Lấy sheet hiện tại
        $sheet = $spreadsheet->getActiveSheet();
        $styleMau = $sheet->getStyle('A11');

        // Lấy đơn vị tạo
        $company_parent = \App\Models\Option::get_option('general','company_parent');
        $company_name   = \App\Models\Option::get_option('general','company_name');
        $company_address   = \App\Models\Option::get_option('general','company_address');
        // Tên sở
        $sheet->setCellValue('A1', $company_parent ?? '');
        // Tên trường 
        $sheet->setCellValue('A2', $company_name ?? '');
        //Ngày xuất:
        $sheet->setCellValue('I7', date('d/m/Y'));

        // Tổ
        $borrowerName = '';
        $nest = request()->nest_id ? \App\Models\Nest::find( request()->nest_id ) : '';
        $borrowerName = $nest->name ?? '';
        $nestID = $nest->id ?? 0;
        $sheet->setCellValue('I6', $borrowerName);

        // Ngày dạy từ
        $dateStart = date('d/m/Y',strtotime($startDate));
        $sheet->setCellValue('F6', $dateStart);

        // Ngày đến
        $dateEnd = date('d/m/Y',strtotime($endDate));
        $sheet->setCellValue('F7', $dateEnd);

        $index = 11;
        $stt = 1; // Khởi tạo biến STT bên ngoài vòng lặp
        foreach ($borrowDevices as $key => $item) {
            // Xử lý xuống dòng trong execl
            $item['device_name'] = str_replace('<br>', "\n",$item['device_name']);
            $sheet->setCellValue('A' . $index, $stt);
            $sheet->setCellValue('B' . $index, $item['borrow_date']);
            $sheet->setCellValue('C' . $index, $item['borrow_date']);
            $sheet->setCellValue('D' . $index, $key + 1);
            $sheet->setCellValue('E' . $index, $item['created_at']);
            $sheet->setCellValue('F' . $index, $item['device_name']);
            $sheet->setCellValue('G' . $index, $item['quantity']);
            $sheet->setCellValue('H' . $index, $item['lecture_name']);
            $sheet->setCellValue('I' . $index, $item['lesson_name']);
            $sheet->setCellValue('J' . $index, $item['room_name']);
            $sheet->setCellValue('K' . $index, $item['session'] == 'Chiều' ? 'C:'.$item['lecture_number'] : 'S:'.$item['lecture_number'] );
            $sheet->setCellValue('L' . $index, $item['borrow_note']);
            $sheet->setCellValue('M' . $index, $item['user_name']);
            
            // Copy style từ A11 cho cả dòng mới
            for ($col = 'A'; $col <= 'M'; $col++) {
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