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
use Modules\Borrow\app\Models\Borrow;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowLabExport {
    public function rules(): array
    {
        $rules = [
            'week' => 'required',
        ];
        return $rules;
    }
    public $messages = [
        'required' => 'Trường là bắt buộc',
    ];
    public function handle($request = null){
        $type = request()->type;

        // Đường dẫn đến mẫu Excel đã có sẵn
        $templatePath = public_path('system/export/'.strtolower($type).'.xlsx');
        // Tạo một Spreadsheet từ mẫu
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($templatePath);

        if( $request->lab_id ){
            $lab_ids[] = $request->lab_id;
        }else{
            $lab_ids = Lab::getAll(true)->pluck('id')->toArray();
        }
        // Sao chép sheet nếu labs_ids > 1
        foreach( $lab_ids as $sheetIndex => $lab_id ){
            if($sheetIndex != count($lab_ids) - 1){
                $originalSheet = $spreadsheet->getActiveSheet();
                $newSheet = clone $originalSheet;
                $newSheet->setTitle('Sheet '.$sheetIndex + 1);
                $spreadsheet->addSheet($newSheet);
            }
        }
        foreach( $lab_ids as $sheetIndex => $lab_id ){
            $spreadsheet = $this->exportSingleSheet($lab_id,$request,$spreadsheet,$sheetIndex);
        }

        $newFilePath = public_path('system/tmp/'.strtolower($type).'.xlsx');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($newFilePath);
        return $newFilePath;
    }

    private function exportSingleSheet($lab_id,$request,$spreadsheet,$sheetIndex = 0){
        // Lấy sheet hiện tại
        $spreadsheet->setActiveSheetIndex($sheetIndex);
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tiêu đề cho sheet
        $lab_name = Lab::find($lab_id)->name ?? '';
        $sheet->setTitle($lab_name);

        // Export single lab
        $request->merge([
            'lab_id' => $lab_id
        ]);
        $borrowLabs = Borrow::getBorrowedLab($request);
        // Tieu de
        $startDayEndDate = Borrow::getStartEndDateFromWeek($request->week);
        $startDay   = $startDayEndDate['startDate']->format('d/m/Y') ?? '';
        $endDay     = $startDayEndDate['endDate']->format('d/m/Y') ?? '';
        $sheet->setCellValue('A2','LỊCH BÁO MƯỢN '.mb_strtoupper($lab_name, 'UTF-8'));
        $borrow_time = "$startDay đến $endDay";
        $sheet->setCellValue('B4',$borrow_time);

        $index = 9; // Bắt đầu từ hàng 9
        $tiet = 1;
        foreach ($borrowLabs as $borrowDate => $borrowLab) {
            $tietSang   = 'C'.$index;
            $tietChieu  = 'E'.$index;
            // Tiet Sang 1,2,4,5
            $sheet->setCellValue('C'.($index+0),$borrowLab[1]['user_name'] ?? '');
            $sheet->setCellValue('C'.($index+1),$borrowLab[2]['user_name'] ?? '');
            $sheet->setCellValue('C'.($index+2),$borrowLab[3]['user_name'] ?? '');
            $sheet->setCellValue('C'.($index+3),$borrowLab[4]['user_name'] ?? '');
            $sheet->setCellValue('C'.($index+4),$borrowLab[5]['user_name'] ?? '');

            $sheet->setCellValue('E'.($index+0),$borrowLab[6]['user_name'] ?? '');
            $sheet->setCellValue('E'.($index+1),$borrowLab[7]['user_name'] ?? '');
            $sheet->setCellValue('E'.($index+2),$borrowLab[8]['user_name'] ?? '');
            $sheet->setCellValue('E'.($index+3),$borrowLab[9]['user_name'] ?? '');
            $sheet->setCellValue('E'.($index+4),$borrowLab[10]['user_name'] ?? '');
            $index+=5;
        }

        return $spreadsheet;
    }
}