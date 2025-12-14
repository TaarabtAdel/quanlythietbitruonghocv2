<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class FormInputSchoolWeek extends Component
{
    protected $selected_id;
    protected $name;
    protected $autoSubmit;
    protected $id;
    protected $defaultYear;

    public function __construct($name = 'school_week', $selectedId = '', $autoSubmit = '', $id = '', $defaultYear = null)
    {
        $this->name = $name;
        $this->selected_id = $selectedId;
        $this->autoSubmit = $autoSubmit;
        $this->id = $id;
        $this->defaultYear = $defaultYear;
    }

    /**
     * Hàm giả lập/truy vấn cấu hình tuần học chi tiết từ Database/Cache.
     * Cấu trúc MỚI: [Năm Học] => ['startWeek1' => 'YYYY-MM-DD', 'numberWeek' => int]
     */
    private function getSchoolYearConfig(): array
    {
        // --- CẤU TRÚC ĐÃ ĐƯỢC CẬP NHẬT THEO YÊU CẦU ---
        return [
            '2024-2025' => [
                'startWeek1' => '2024-09-05', // YYYY-MM-DD
                'numberWeek' => 38
            ], 
            '2025-2026' => [
                'startWeek1' => '2025-09-05',
                'numberWeek' => 38
            ],
            '2026-2027' => [
                'startWeek1' => '2026-09-05',
                'numberWeek' => 38
            ], 
        ];
        // ------------------------------------------------
    }
    
    /**
     * Lấy năm học hiện tại theo quy tắc: YYYY-YYYY+1
     */
    private function getCurrentSchoolYear(): string
    {
        $currentMonth = date('n'); 
        $currentYear = date('Y');
        
        // Sau tháng 8 (hoặc tháng 9), năm học là YYYY-YYYY+1
        if ($currentMonth >= 9) { 
            return $currentYear . '-' . ($currentYear + 1);
        }
        // Đầu năm (trước tháng 9), năm học là YYYY-1-YYYY
        return ($currentYear - 1) . '-' . $currentYear;
    }


    public function render(): View|string
    {
        $schoolConfig = $this->getSchoolYearConfig();
        
        $defaultYear = $this->defaultYear 
            ? $this->defaultYear 
            : $this->getCurrentSchoolYear();
            
        if (!array_key_exists($defaultYear, $schoolConfig) && !empty($schoolConfig)) {
            $defaultYear = array_key_first($schoolConfig);
        }
        $params = [
            'selected_id'   => $_GET['sw_display_'.$this->name] ?? '',
            'name'          => $this->name,
            'autoSubmit'    => $this->autoSubmit,
            'id'            => $this->id ?: 'school-week-input-' . uniqid(),
            'schoolConfig'  => $schoolConfig, // Dữ liệu cấu hình đã được cập nhật
            'defaultYear'   => $defaultYear,  
        ];

        return view('components.form-input-school-week', $params);
    }
}