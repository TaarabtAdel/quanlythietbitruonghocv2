<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormStatus extends Component
{
    protected $model;
    protected $item;
    protected $showAll;
    /**
     * Create a new component instance.
     */
    public function __construct($model,$status = null,$showAll = 0)
    {
        $this->model = $model;
        $this->status = $status;
        $this->showAll = $showAll;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        $params = [
            'model' => $this->model,
            'status' => $this->status,
            'showAll' => $this->showAll,
        ];
        return view('components.form-status',$params);
    }
}
