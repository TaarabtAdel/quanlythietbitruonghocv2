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
use App\Models\Device;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowDeviceExport {
    protected $templateFile = '';
    public function rules(): array
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];
        return $rules;
    }
    public $messages = [
        'required' => 'Trường yêu cầu',
    ];
    public function handle($request = null){
        try {
            $type = request()->type;
            $exportBy = request()->export_by;
            $query = User::query(true)->with(['nest']);
            
            $startDate = request()->start_date;
            $endDate = request()->end_date;

            $items = $query->join('borrows', 'users.id', '=', 'borrows.user_id')
            ->whereBetween('borrows.borrow_date', [$startDate, $endDate])
            ->whereIn('borrows.status', [0, 1])
            ->distinct()
            ->select('users.*')
            ->get();

            $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');
            if (!file_exists($templatePath)) {
                throw new \Exception("Template file not found: " . $templatePath);
            }

            $tmpDir = public_path('system/tmp');
            if (!file_exists($tmpDir)) {
                mkdir($tmpDir, 0777, true);
            }

            $reader = IOFactory::createReader("Xlsx");
            $spreadsheet = $reader->load($templatePath);
            
            $startDate_fm = date('d/m/Y',strtotime($startDate));
            $endDate_fm = date('d/m/Y',strtotime($endDate));
            $date = Carbon::createFromFormat('d/m/Y', $endDate_fm);
            $year = $request->year;
            
            $company_parent = \App\Models\Option::get_option('general','company_parent');
            $company_name   = \App\Models\Option::get_option('general','company_name');
            $title = mb_strtoupper($company_parent.' '.$company_name,'UTF-8');      
              
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
            // Từ ngày - Đến ngày
            $sheet->setCellValue('D6', $startDate_fm);
            $sheet->setCellValue('F6', $endDate_fm);
            // Năm học
            $sheet->setCellValue('E7',$year);

            // Duyệt qua danh sách mượn thiết bị
            $index = 10; // Bắt đầu từ hàng 10
            $stt = 1; // Khởi tạo biến STT

            foreach ($items as $item) {
                $user = $item;
                if ($exportBy == 'tiet') {
                    $borrows = Borrow::with('the_devices')
                    ->where('user_id',$user->id)
                    ->whereBetween('borrows.borrow_date', [$startDate, $endDate])
                    ->whereIn('borrows.status', [0,1])
                    ->get();

                    $purposes = [];
                    foreach ($borrows as $borrow) {
                        $purposes[$borrow->borrow_purpose][$borrow->id] = $borrow->the_devices->groupBy('tiet')->count();
                    }
                    $purposeCounts = [];
                    foreach ($purposes as $purpose => $borrow) {
                        $purposeCounts[$purpose] = array_sum($borrow);
                    }
                } else {
                    $purposeCounts = DB::table('borrows')
                        ->select('borrow_purpose', DB::raw('count(*) as total'))
                        ->whereIn('status', [0,1])
                        ->where('user_id', $user->id)
                        ->whereBetween('borrow_date', [$startDate, $endDate])
                        ->groupBy('borrow_purpose')
                        ->pluck('total', 'borrow_purpose');
                }

                $purposeList = \App\Models\Borrow::get_borrow_purposes();
                $statistics = [];
                foreach ($purposeList as $key => $label) {
                    $statistics[$key] = 0;
                }
                foreach ($purposeCounts as $purpose => $count) {
                    $statistics[$purpose] = $count ? $count : 0;
                }
                $sheet->setCellValue('A' . $index, $stt);
                $sheet->setCellValue('B' . $index, $item->name ?? '');
                $sheet->setCellValue('C' . $index, $item->nest->name ?? '');
                $col = 'D';

                $colIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col);
                $rowIndex = 9;

                foreach ($purposeList as $key => $label) {
                    // Thiết lập tên các cột
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->setCellValue($column . $rowIndex, $label);

                    // Đưa dữ liệu vào
                    $sheet->setCellValue($col . $index, $statistics[$key] ? $statistics[$key] : 0);

                    // Copy style từ A11 cho cả dòng mới
                    for ($colStyle = 'A'; $colStyle <= 'I'; $colStyle++) { 
                        $sheet->duplicateStyle($styleMau, $colStyle . $index); 
                    } 
                    $col++;
                    $colIndex++;
                }

                $index++;
                $stt++;
            }
            
            $spreadsheet->setActiveSheetIndex(0);
            $newFilePath = public_path('system/tmp/'.strtolower($type).'-'.date('d-m-Y-H-i-s').'.xlsx');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($newFilePath);

            if (!file_exists($newFilePath)) {
                throw new \Exception("Failed to create export file");
            }

            return $newFilePath;

        } catch (\Exception $e) {
            // \Log::error('Excel export error: ' . $e->getMessage());
            throw $e;
        }
    }
}