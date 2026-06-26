<?php

namespace App\View\Components;

use App\Support\Api\SchoolCalendar;
use Illuminate\View\Component;
use Illuminate\View\View;

class FormInputSchoolYears extends Component
{
    protected $selected_id;

    protected $name;

    protected $autoSubmit;

    public function __construct($name = 'school_year', $selectedId = '', $autoSubmit = '', $id = '')
    {
        $this->name = $name;
        $this->selected_id = $selectedId;
        $this->autoSubmit = $autoSubmit;
        $this->id = $id;
    }

    public function render(): View|string
    {
        $items = array_map(function (array $row) {
            $item = new \stdClass;
            $item->id = $row['id'];
            $item->name = $row['name'];

            return $item;
        }, SchoolCalendar::schoolYears());

        return view('components.form-input-select2', [
            'selected_id' => $this->selected_id,
            'name' => $this->name,
            'autoSubmit' => $this->autoSubmit,
            'id' => $this->id,
            'items' => $items,
        ]);
    }
}
