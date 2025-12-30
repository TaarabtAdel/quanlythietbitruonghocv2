<?php

namespace App\Imports;

use App\Models\Curriculum;
use App\Models\CurriculumDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class CurriculumImport implements ToCollection
{
    // Thuộc tính này sẽ được gán giá trị từ bên ngoài trước khi thực hiện Import
    public $request_data = [
        'school_year' => '',
        'department_id' => '',
        'subject_type' => '',
        'grade' => '',
    ];

    public $rules = [
        'school_year' => 'required|string',
        'subject_type' => 'required|string',
        'department_id' => 'required|exists:departments,id',
        'grade' => 'required|string',
    ];

    public $messages = [
        'school_year.required' => 'Vui lòng chọn năm học',
        'subject_type.required' => 'Vui lòng chọn phân môn',
        'department_id.required' => 'Vui lòng chọn bộ môn',
        'grade.required' => 'Vui lòng chọn khối',
    ];

    /**
     * Khởi tạo không tham số theo yêu cầu
     */
    public function __construct()
    {
        // Constructor trống
    }

    public function collection(Collection $rows)
    {
        // 1. Bỏ qua dòng đầu tiên (header)
        $rows->shift();
        
        // 2. Lọc các dòng rỗng
        $rows = $rows->filter(function ($row) {
            return !empty($row[2]);
        });

        // 3. Validation dữ liệu Excel
        Validator::make($rows->toArray(), [
            '*.2' => 'required',
        ],[
            '*.2.required' => 'Tên bài học ở hàng :attribute là bắt buộc.',
        ])->validate();

        DB::beginTransaction();
        try {
            // Lấy dữ liệu từ thuộc tính công khai $request_data
            $schoolYear   = $this->request_data['school_year'] ?? null;
            $departmentId = $this->request_data['department_id'] ?? null;
            $grade        = $this->request_data['grade'] ?? null;
            $subjectType  = $this->request_data['subject_type'] ?? null;

            if (empty($departmentId)) {
                throw new \Exception('Vui lòng chọn bộ môn');
            }

            // 4. Tìm hoặc tạo curriculum
            $curriculum = Curriculum::firstOrCreate([
                'academic_year' => $schoolYear,
                'department_id' => $departmentId,
                'grade'         => $grade,
                'subject_type'  => $subjectType,
            ]);

            // 5. Chuẩn bị dữ liệu chi tiết
            $details = [];
            foreach ($rows as $row) {
                // Trim dữ liệu từng ô
                $c0 = isset($row[0]) ? trim($row[0]) : null; // Week
                $c1 = isset($row[1]) ? trim($row[1]) : null; // Lesson Number
                $c2 = isset($row[2]) ? trim($row[2]) : null; // Lesson Name
                $c3 = isset($row[3]) ? trim($row[3]) : null; // Note

                if (empty($c2)) continue;

                $details[] = [
                    'curriculum_id' => $curriculum->id,
                    'week'          => $c0 !== '' ? $c0 : null,
                    'lesson_number' => $c1 !== '' ? $c1 : null,
                    'lesson_name'   => $c2,
                    'note'          => $c3,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            // 6. Lưu vào DB
            if (!empty($details)) {
                CurriculumDetail::insert($details);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}