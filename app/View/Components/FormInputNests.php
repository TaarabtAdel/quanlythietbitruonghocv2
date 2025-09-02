<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormInputNests extends Component
{
    protected $selected_id;
    protected $name;
    protected $autoSubmit;
    /**
     * Create a new component instance.
     */
    public function __construct($name = 'nest_id',$selectedId = '',$autoSubmit = '')
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
        $items = \App\Models\Nest::all();
        $params = [
            'selected_id'   => $this->selected_id,
            'name'          => $this->name,
            'autoSubmit'    => $this->autoSubmit,
            'items'         => $items,
        ];
        return view('admintheme::components.form-input-select2',$params);
    }
}
