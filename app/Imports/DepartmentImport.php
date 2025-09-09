<?php

namespace App\Imports;

use App\Models\Department;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;

class DepartmentImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function collection(Collection $rows)
    {
        // bỏ qua hàng tiêu đề
        $rows->shift();
        foreach( $rows as $key => $row ){
            if (empty($row[1])) {
                unset($rows[$key]);
            }
        }
        // bỏ qua nếu teen trùng
        Validator::make($rows->toArray(), [
            '*.1' => 'required',
        ],[
            '*.1.required' => 'Bộ môn hàng :attribute là bắt buộc.',
        ])->validate();
        
        foreach ($rows as $row) {
            foreach( $row as $k => $v ){
                $row[$k] = trim($v);
            }
            $data = [
                'name' => $row[1],
            ];
            $item = Department::where('name',$data['name'])->first();
            if ($item) {
                $item->update($data);
            }else {
                Department::create($data);
            }
        }
    }
}