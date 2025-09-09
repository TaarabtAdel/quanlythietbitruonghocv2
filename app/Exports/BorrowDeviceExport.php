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
              
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1',$title);
            $sheet->setCellValue('D6',$startDate_fm);
            $sheet->setCellValue('F6',$endDate_fm);
            $sheet->setCellValue('E7',$year);

            $index = 10;
            $stt = 1;

            $phongDeviceIds = Device::where('name', 'like', 'Phòng%')->pluck('id')->toArray();
            $phongDeviceIds[] = 0;
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
                    // $borrows = DB::table('borrows')
                    // ->join('borrow_devices', 'borrows.id', '=', 'borrow_devices.borrow_id')
                    // ->whereIn('borrows.status', [0,1])
                    // ->where('borrows.user_id', $user->id)
                    // ->whereBetween('borrows.borrow_date', [$startDate, $endDate])
                    // ->get()->toArray();
                    // dd($borrows);
                    // $purposeCounts = DB::table('borrows')
                    //     ->join('borrow_devices', 'borrows.id', '=', 'borrow_devices.borrow_id')
                    //     ->select('borrows.borrow_purpose', DB::raw('COUNT(DISTINCT borrow_devices.tiet) as total'))
                    //     ->whereIn('borrows.status', [0,1])
                    //     ->where('borrows.user_id', $user->id)
                    //     ->whereBetween('borrows.borrow_date', [$startDate, $endDate])
                    //     ->groupBy('borrows.borrow_purpose')
                    //     ->pluck('total', 'borrow_purpose');
                } else {
                    $purposeCounts = DB::table('borrows')
                        ->select('borrow_purpose', DB::raw('count(*) as total'))
                        ->whereIn('status', [0,1])
                        ->where('user_id', $user->id)
                        ->whereBetween('borrow_date', [$startDate, $endDate])
                        ->groupBy('borrow_purpose')
                        ->pluck('total', 'borrow_purpose');
                }

                $purposeList = \Modules\AdminBorrow\app\Models\Borrow::get_borrow_purposes();
                $statistics = [];
                
                foreach ($purposeList as $key => $label) {
                    $statistics[$key] = 0;
                }

                foreach ($purposeCounts as $purpose => $count) {
                    $statistics[$purpose] = $count;
                }

                $sheet->setCellValueExplicit('A' . $index, $stt, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getStyle('A' . $index)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL);
                $sheet->setCellValue('B' . $index, $item->name ?? '');
                $sheet->setCellValue('C' . $index, $item->nest->name ?? '');
                
                $col = 'D';
                foreach ($purposeList as $key => $label) {
                    $sheet->setCellValue($col . '9', $label);
                    $sheet->setCellValue($col . $index, $statistics[$key]);
                    
                    $sheet->duplicateStyle(
                        $sheet->getStyle('D1:D' . $sheet->getHighestRow()),
                        $col . '1:' . $col . $sheet->getHighestRow()
                    );
                    
                    $col++;
                }

                $index++;
                $stt++;
            }
            
            $spreadsheet->setActiveSheetIndex(0);
            $fileName = 'bao-cao-muon-thiet-bi-phong-bo-mon-' . date('Y-m-d') . '.xlsx';
            $newFilePath = public_path('system/tmp/' . $fileName);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($newFilePath);

            if (!file_exists($newFilePath)) {
                throw new \Exception("Failed to create export file");
            }

            return $newFilePath;

        } catch (\Exception $e) {
            \Log::error('Excel export error: ' . $e->getMessage());
            throw $e;
        }
    }
}