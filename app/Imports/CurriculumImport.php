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
    protected $school_year;
    protected $department_id;
    protected $subject_type;
    protected $grade;

    public function __construct($school_year = null, $department_id = null, $subject_type = null, $grade = null)
    {
        $this->school_year = $school_year;
        $this->department_id = $department_id;
        $this->subject_type = $subject_type;
        $this->grade = $grade;
    }

    public function collection(Collection $rows)
    {
        // Bỏ qua dòng đầu tiên (header)
        $rows->shift();
        
        // Lọc các dòng rỗng
        foreach( $rows as $key => $row ){
            if (empty($row[0])) {
                unset($rows[$key]);
            }
        }

        // Validation - cột đầu tiên là tên bài học (lesson_name)
        Validator::make($rows->toArray(), [
            '*.3' => 'required', // Tên bài học là bắt buộc (cột D)
        ],[
            '*.3.required' => 'Tên bài học ở hàng :attribute là bắt buộc.',
        ])->validate();

        DB::beginTransaction();
        try {
            // Tạo hoặc tìm curriculum dựa trên academic_year, department_id, grade
            // Format Excel: Cột A = sub_subject_type, Cột B = week, Cột C = lesson_number, Cột D = lesson_name, Cột E = note
            
            if (empty($this->department_id)) {
                throw new \Exception('Vui lòng chọn bộ môn');
            }

            // Tìm hoặc tạo curriculum
            $curriculum = Curriculum::where('academic_year', $this->school_year)
                ->where('department_id', $this->department_id)
                ->where('grade', $this->grade)
                ->first();

            if (!$curriculum) {
                $curriculum = Curriculum::create([
                    'academic_year' => $this->school_year,
                    'department_id' => $this->department_id,
                    'grade' => $this->grade,
                ]);
            }

            // Xóa các chi tiết cũ nếu đang cập nhật (hoặc có thể giữ lại)
            // CurriculumDetail::where('curriculum_id', $curriculum->id)->delete();

            // Thêm các chi tiết mới
            $details = [];
            
            foreach ($rows as $row) {
                foreach( $row as $k => $v ){
                    $row[$k] = trim($v ?? '');
                }
                
                // Bỏ qua dòng không có tên bài học
                if (empty($row[3])) {
                    continue;
                }

                $details[] = [
                    'curriculum_id' => $curriculum->id,
                    'sub_subject_type' => $row[0] ?? null, // Loại phân môn (cột A)
                    'week' => !empty($row[1]) ? (int)$row[1] : null, // Tuần (cột B)
                    'lesson_number' => !empty($row[2]) ? (int)$row[2] : null, // Số tiết (cột C)
                    'lesson_name' => $row[3] ?? null, // Tên bài học (cột D)
                    'note' => $row[4] ?? null, // Ghi chú (cột E)
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

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
