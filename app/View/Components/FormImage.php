<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormImage extends Component
{
    /**
     * Create a new component instance.
     */
    protected $imageUrl;
    public function __construct($name = '',$imageUrl = '',$upload = 1,$accept = '*')
    {
        $this->name         = $name;
        $this->imageUrl     = $imageUrl;
        $this->upload       = $upload;
        $this->accept       = $accept;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        $params = [
            'name'      => $this->name,
            'imageUrl'  => $this->imageUrl,
            'upload'    => $this->upload,
            'accept'    => $this->accept,
        ];
        return view('components.form-image',$params);
    }
}
