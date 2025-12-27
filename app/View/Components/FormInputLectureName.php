<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class FormInputLectureName extends Component
{
    protected $tiet;
    protected $name;
    protected $value;
    protected $defaultYear;
    /**
     * Create a new component instance.
     */
    public function __construct($name = 'lecture_name',$value = null,$tiet = 0)
    {
        $this->name = $name;
        $this->value = $value;
        $this->tiet = $tiet;
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

    private function getSchoolYears(){
        $items = [];

        // Cache 1 ngày (1440 phút), có thể chỉnh theo nhu cầu
        $first_borrow = Cache::remember('first_borrow_record', 1440, function () {
            return \App\Models\Borrow::orderBy('created_at', 'ASC')->first();
        });

        $start_year = $first_borrow ? date('Y', strtotime($first_borrow->created_at)) : date('Y');

        for ($i = $start_year; $i <= date('Y'); $i++) {
            $school_year = $i . '-' . ($i + 1);
            $school_year_obj = new \stdClass;
            $school_year_obj->id = $school_year;
            $school_year_obj->name = $school_year;
            $items[] = $school_year_obj;
        }
        return $items;
    }

    private function getGrades(){
        $rooms = \App\Models\Room::pluck('name');
        $grades = [];
        foreach ($rooms as $room) {
            // Regex lấy số ở đầu chuỗi (VD: 10A1 -> 10, 1A -> 1)
            if (preg_match('/^(\d+)/', $room, $matches)) {
                $grade = (int)$matches[1];
                $grades[$grade] = "Khối $grade";
            }
        }
        ksort($grades); // Sắp xếp theo key (số khối)

        $items = [];
        foreach ($grades as $id => $name) {
            $items[] = (object)['id' => $id, 'name' => $name];
        }
        return $items;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        $defaultYear = $this->defaultYear 
            ? $this->defaultYear 
            : $this->getCurrentSchoolYear();

        $defaultGrade = '';
        $defaultSubjectType = '';
        $defaultSubject = '';


        $schoolYears = $this->getSchoolYears();
        $grades  = $this->getGrades();
        $subjects = \App\Models\Department::all();
        $subjectTypes = [
            'mon_chinh' => 'Môn chính',
            'chuyen_de' => 'Chuyên đề',
        ];

        $params = [
            'name' => $this->name,
            'value' => $this->value,
            'tiet' => $this->tiet,
            'defaultYear' => $defaultYear,
            'defaultGrade' => $defaultGrade,
            'defaultSubjectType' => $defaultSubjectType,
            'defaultSubject' => $defaultSubject,
            'schoolYears' => $schoolYears,
            'grades' => $grades,
            'subjects' => $subjects,
            'subjectTypes' => $subjectTypes,
        ];
        return view('components.form-input-lecture_name',$params);
    }
}
