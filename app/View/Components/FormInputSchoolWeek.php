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

    private function getSchoolYearConfig(): array
    {
        $firstBorrow = \Illuminate\Support\Facades\Cache::remember('first_borrow_record', 1440, function () {
            return \App\Models\Borrow::orderBy('created_at', 'ASC')->first();
        });

        $startYear = $firstBorrow ? (int)date('Y', strtotime($firstBorrow->created_at)) : (int)date('Y');
        
        $currentYear = (int)date('Y');
        $configs = [];

        for ($i = $startYear; $i <= $currentYear; $i++) {
            $yearKey = $i . '-' . ($i + 1);
            
            $configs[$yearKey] = [
                'startWeek1' => "{$i}-09-05", // Mặc định khai giảng 05/09 hàng năm
                'numberWeek' => 38            // Số tuần mặc định
            ];
        }

        return $configs;
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