<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AdminModel extends MainModel
{
    public static function getApprovedOnWeek($table){
        $model = '\App\Models\\' . $table;
        $currentWeek    = Carbon::now()->format('Y-\WW');
        $startDateEndDate = $model::getStartEndDateFromWeek($currentWeek);
        $startDateEndDate = array_values($startDateEndDate);
        $count_approved = $model::where('status',1)->whereBetween('borrow_date', $startDateEndDate)->count();
        $count_inapproved = $model::where('status',0)->whereBetween('borrow_date', $startDateEndDate)->count();
        $count_devides = \App\Models\BorrowDevice::whereBetween('borrow_date', $startDateEndDate)->count();
        $count_labs = \App\Models\BorrowDevice::whereBetween('borrow_date', $startDateEndDate)->where('lab_id','>',0)->groupBy('lab_id')->count();
        return $param = [
            'count_approved' => $count_approved,
            'count_inapproved' => $count_inapproved,
            'count_devides' => $count_devides,
            'count_labs' => $count_labs
        ];
    }
    public function getStatusFmAttribute(){
        if ($this->deleted_at) {
            return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">'.__('sys.inactive').'</span>';
        }else{
            return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">'.__('sys.active').'</span>';
        }
    }
}