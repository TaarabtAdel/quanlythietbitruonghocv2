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

        // Validation - cột đầu tiên là tên môn học (subject_name)
        Validator::make($rows->toArray(), [
            '*.0' => 'required', // Tên môn học là bắt buộc
        ],[
            '*.0.required' => 'Tên môn học ở hàng :attribute là bắt buộc.',
        ])->validate();

        DB::beginTransaction();
        try {
            // Tạo hoặc tìm curriculum dựa trên academic_year, subject_name, grade
            // Format Excel: Cột A = subject_name, Cột B = sub_subject_type, Cột C = week, Cột D = lesson_number, Cột E = lesson_name, Cột F = note
            
            // Lấy subject_name từ dòng đầu tiên (hoặc có thể từ form)
            $subjectName = $rows->first()[0] ?? '';
            
            if (empty($subjectName)) {
                throw new \Exception('Không tìm thấy tên môn học trong file Excel');
            }

            // Tìm hoặc tạo curriculum
            $curriculum = Curriculum::where('academic_year', $this->school_year)
                ->where('subject_name', $subjectName)
                ->where('grade', $this->grade)
                ->first();

            if (!$curriculum) {
                $curriculum = Curriculum::create([
                    'academic_year' => $this->school_year,
                    'subject_name' => $subjectName,
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
                
                if (empty($row[0])) {
                    continue;
                }

                $details[] = [
                    'curriculum_id' => $curriculum->id,
                    'sub_subject_type' => $row[1] ?? null, // Loại phân môn
                    'week' => !empty($row[2]) ? (int)$row[2] : null, // Tuần
                    'lesson_number' => !empty($row[3]) ? (int)$row[3] : null, // Số tiết
                    'lesson_name' => $row[4] ?? null, // Tên bài học
                    'note' => $row[5] ?? null, // Ghi chú
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
