<?php

namespace App\Imports;

use App\Models\DeviceType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;

class DeviceTypeImport implements ToCollection
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
            '*.1.required' => 'Loại thiết bị hàng :attribute là bắt buộc.',
        ])->validate();

        foreach ($rows as $row) {
            $data = [
                'name' => trim($row[1]),
            ];
            $item = DeviceType::where('name',$data['name'])->first();
            if ($item) {
                $item->update($data);
            }else {
                DeviceType::create($data);
            }
        }
    }
}