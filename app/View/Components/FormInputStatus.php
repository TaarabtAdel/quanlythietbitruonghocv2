<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormInputStatus extends Component
{
    protected $model;
    protected $item;
    protected $autoSubmit;
    /**
     * Create a new component instance.
     */
    public function __construct($name = 'status',$status = null,$autoSubmit = 0)
    {
        $this->name = $name;
        $this->status = $status;
        $this->autoSubmit = $autoSubmit;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        $params = [
            'name' => $this->name,
            'status' => $this->status,
            'autoSubmit' => $this->autoSubmit,
        ];
        return view('components.form-input-status',$params);
    }
}
