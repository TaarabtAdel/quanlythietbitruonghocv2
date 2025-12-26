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

        // Validation - cột thứ ba là tên bài học (lesson_name)
        Validator::make($rows->toArray(), [
            '*.2' => 'required', // Tên bài học là bắt buộc (cột C)
        ],[
            '*.2.required' => 'Tên bài học ở hàng :attribute là bắt buộc.',
        ])->validate();

        DB::beginTransaction();
        try {
            // Tạo hoặc tìm curriculum dựa trên academic_year, department_id, grade, subject_type
            // Format Excel: Cột A = week, Cột B = lesson_number, Cột C = lesson_name, Cột D = note
            
            if (empty($this->department_id)) {
                throw new \Exception('Vui lòng chọn bộ môn');
            }

            // Tìm hoặc tạo curriculum
            $curriculum = Curriculum::where('academic_year', $this->school_year)
                ->where('department_id', $this->department_id)
                ->where('grade', $this->grade)
                ->where('subject_type', $this->subject_type)
                ->first();

            if (!$curriculum) {
                $curriculum = Curriculum::create([
                    'academic_year' => $this->school_year,
                    'department_id' => $this->department_id,
                    'grade' => $this->grade,
                    'subject_type' => $this->subject_type,
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
                if (empty($row[2])) {
                    continue;
                }

                $details[] = [
                    'curriculum_id' => $curriculum->id,
                    'week' => !empty($row[0]) ? (int)$row[0] : null, // Tuần (cột A)
                    'lesson_number' => !empty($row[1]) ? (int)$row[1] : null, // Số tiết (cột B)
                    'lesson_name' => $row[2] ?? null, // Tên bài học (cột C)
                    'note' => $row[3] ?? null, // Ghi chú (cột D)
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
