<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormInputGrade extends Component
{
    protected $selected_id;
    protected $name;
    protected $autoSubmit;
    /**
     * Create a new component instance.
     */
    public function __construct($name = 'grade',$selectedId = '',$autoSubmit = '')
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

        $params = [
            'selected_id'   => $this->selected_id,
            'name'          => $this->name,
            'autoSubmit'    => $this->autoSubmit,
            'items'         => $items,
        ];
        return view('components.form-input-select',$params);
    }
}
