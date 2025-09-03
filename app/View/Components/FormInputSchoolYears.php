<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class FormInputSchoolYears extends Component
{
    protected $selected_id;
    protected $name;
    protected $autoSubmit;

    public function __construct($name = 'school_year', $selectedId = '', $autoSubmit = '')
    {
        $this->name = $name;
        $this->selected_id = $selectedId;
        $this->autoSubmit = $autoSubmit;
    }

    public function render(): View|string
    {
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

        $params = [
            'selected_id'   => $this->selected_id,
            'name'          => $this->name,
            'autoSubmit'    => $this->autoSubmit,
            'items'         => $items,
        ];

        return view('components.form-input-select2', $params);
    }
}

