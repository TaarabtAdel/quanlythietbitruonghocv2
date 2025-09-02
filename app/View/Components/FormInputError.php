<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormInputError extends Component
{
    protected $field;
    /**
     * Create a new component instance.
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        $params = [
            'field' => $this->field
        ];
        return view('components.form-input-error',$params);
    }
}
