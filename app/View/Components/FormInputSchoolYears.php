<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormInputSchoolYears extends Component
{
    protected $selected_id;
    protected $name;
    protected $autoSubmit;
    /**
     * Create a new component instance.
     */
    public function __construct($name = 'school_year',$selectedId = '',$autoSubmit = '')
    {
        $this->name = $name;
        $this->selected_id = $selectedId;
        $this->autoSubmit = $autoSubmit;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        $items = [];
        for( $i = 2022; $i <= date('Y'); $i++ ){
            $school_year = $i .'-'.$i + 1;
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
        return view('admintheme::components.form-input-select2',$params);
    }
}
