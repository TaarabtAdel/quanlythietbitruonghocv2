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

class BorrowDevicesUserExport {
    protected $templateFile = '';
    public function rules(): array
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
            'user_id' => 'required',
        ];
        return $rules;
    }

    public $messages = [
        'required' => 'Trường bắt buộc'
    ];
    public function handle($request = null){
        // $id = request()->id;
        $type = request()->type;
        // Lấy thông tin người dùng và mượn thiết bị
        // $borrow = Borrow::find($id);
        $user = User::find(request()->user_id);
        $query = Borrow::whereIn('status',[0,1]);
        $query = $query->where('user_id', request()->user_id);

        // lấy ra theo ngày
        $startDate = request()->start_date;
        $endDate = request()->end_date;
        $query->whereBetween('borrow_date', [$startDate, $endDate]);
        
        $borrows = $query->get();
        $export_by = request()->export_by ? request()->export_by : 'device';
        if($export_by == 'device'){
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

            $sheet->setCellValue('F6', $user->name ?? '');
            $sheet->setCellValue('I6', $user->nest->name ?? '');
            $sheet->setCellValue('F7', $user->nest->name ?? '');

            // Duyệt qua danh sách mượn thiết bị
            $index = 11; // Bắt đầu từ hàng 10
            $stt = 1; // Khởi tạo biến STT
            foreach ($borrows as $borrow) {
                foreach ($borrow->the_devices as $device) {
                    $lab_name = $device->lab->name ?? '';
                    $device_name = $device->device->name ?? '';
                    $sheet->setCellValue('A' . $index, $stt);
                    $sheet->setCellValue('B' . $index, date('d/m/Y',strtotime($device->borrow_date)));
                    $sheet->setCellValue('C' . $index, date('d/m/Y',strtotime($device->borrow_date)));
                    $sheet->setCellValue('D' . $index, $borrow->id);
                    $sheet->setCellValue('E' . $index, date('d/m/Y',strtotime($device->created_at)));
                    $sheet->setCellValue('F' . $index, $device_name ? $device_name : $lab_name);
                    $sheet->setCellValue('G' . $index, $device->quantity);
                    $sheet->setCellValue('H' . $index, $device['session'] == 'Chiều' ? 'C:'.$device['lecture_number'] : 'S:'.$device['lecture_number'] );
                    $sheet->setCellValue('I' . $index, $device->lesson_name);
                    $sheet->setCellValue('J' . $index, $device->room->name ?? '');
                    $sheet->setCellValue('K' . $index, $borrow->borrow_note ?? '');
                    $sheet->setCellValue('L' . $index, $user->name);

                    // Copy style từ A11 cho cả dòng mới
                    for ($col = 'A'; $col <= 'L'; $col++) {
                        $sheet->duplicateStyle($styleMau, $col . $index);
                    }

                    $index++;
                    $stt++;
                }
            }

            $spreadsheet->setActiveSheetIndex(0);
            $newFilePath = public_path('system/tmp/'.strtolower($type).'-'.$user->id.'-'.date('d-m-Y-H-i-s').'.xlsx');
        }else{
            // Đường dẫn đến mẫu Excel đã có sẵn
            $type = 'BorrowDetail';
            $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');
            // Tạo một Spreadsheet từ mẫu
            $reader = IOFactory::createReader("Xlsx");
            $spreadsheet = $reader->load($templatePath);

            // Lấy đơn vị tạo
            // Lấy đơn vị tạo
            $company_parent = \App\Models\Option::get_option('general','company_parent');
            $company_name   = \App\Models\Option::get_option('general','company_name');
            $company_address   = \App\Models\Option::get_option('general','company_address');

            foreach ($borrows as $key => $borrow) {
                // Lấy template sheet (sheet đầu tiên)
                $templateSheet = $spreadsheet->getSheet(0);
                // Clone template sheet và thêm vào spreadsheet
                $sheet = clone $templateSheet;
                // Đặt tên tạm thời độc nhất cho sheet mới
                $sheet->setTitle('TempSheet_' . uniqid());
                $spreadsheet->addSheet($sheet);

                $sheet->setTitle(($key + 1) .' - Mã '. $borrow->id);

                $styleMau = $sheet->getStyle('A11');
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
                $sheet->setCellValue('G6', $borrow->id);
                //Ngày tạo:
                $sheet->setCellValue('G7', date('d/m/Y',strtotime($borrow->created_at)));

                // Duyệt qua danh sách mượn thiết bị
                $index = 11; // Bắt đầu từ hàng 10
                $stt = 1; // Khởi tạo biến STT
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
            }
            
            // Xóa sheet template (sheet 0) sau khi đã clone xong
            $spreadsheet->removeSheetByIndex(0);
            $newFilePath = public_path('system/tmp/'.strtolower($type).'-'.$user->id.'-'.date('d-m-Y-H-i-s').'.xlsx');
        }
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($newFilePath);
        return $newFilePath;
    }
}
