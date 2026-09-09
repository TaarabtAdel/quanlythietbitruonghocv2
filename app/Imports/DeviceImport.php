<?php

namespace App\Imports;

use App\Models\Device;
use App\Models\DeviceType;
use App\Models\Department;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;

class DeviceImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    function getDeviceType($name)
    {
        $deviceType = DeviceType::where('name', 'LIKE' , '%'.$name.'%')->first();
        if ($deviceType) {
            return $deviceType->id;
        }else {
            $data['name'] = $name;
            $item = DeviceType::create($data);
            return $item->id;
        }
    }

    function getDepartmant($name)
    {
        $department = Department::where('name', 'LIKE' , '%'.$name.'%')->first();
        if ($department) {
            return $department->id;
        }else {
            $data['name'] = $name;
            $item = Department::create($data);
            return $item->id;
        }
    }
    
    public function collection(Collection $rows)
    {
        $this->importRows($rows);
    }

    public function importFromPath(string $path): void
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);

        try {
            foreach ($spreadsheet->getAllSheets() as $worksheet) {
                $rows = collect($worksheet->toArray(null, true, true, false));
                $this->importRows($rows);
            }
        } finally {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }
    }

    protected function importRows(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $rows = $rows->values();
        $rows->shift();

        foreach ($rows as $key => $row) {
            if (empty($row[1])) {
                unset($rows[$key]);
            }
        }

        $rows = $rows->values();
        if ($rows->isEmpty()) {
            return;
        }

        Validator::make($rows->toArray(), [
            '*.1' => 'required',
            '*.8' => 'required',
            '*.9' => 'required',
        ], [
            '*.1.required' => 'Tên thiết bị hàng :attribute là bắt buộc.',
            '*.4.required' => 'Số lượng hàng :attribute là bắt buộc.',
            '*.4.numeric' => 'Số lượng hàng :attribute phải là một số.',
            '*.8.required' => 'Thể loại hàng :attribute thiết bị là bắt buộc.',
            '*.9.required' => 'Bộ môn là hàng :attribute bắt buộc.',
        ])->validate();

        foreach ($rows as $row) {
            foreach ($row as $k => $v) {
                $row[$k] = is_scalar($v) ? trim((string) $v) : $v;
            }
            $data = [
                'name' => $row[1],
                'country_name' => $row[2] ?? null,
                'year' => $row[3] ?? null,
                'quantity' => $row[4] ?? null,
                'unit' => $row[5] ?? null,
                'price' => $row[6] ?? null,
                'note' => $row[7] ?? null,
                'device_type_id' => $this->getDeviceType($row[8]),
                'department_id' => $this->getDepartmant($row[9]),
                'deleted_at' => null,
            ];
            $item = Device::where('name', $data['name'])->first();
            if ($item) {
                $item->update($data);
            } else {
                Device::create($data);
            }
        }
    }
}