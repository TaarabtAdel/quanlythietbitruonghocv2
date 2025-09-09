<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function authorize($action = '', $model = ''){
        return true;
    }
}
