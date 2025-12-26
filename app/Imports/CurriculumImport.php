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

        // Validation
        Validator::make($rows->toArray(), [
            '*.0' => 'required', // Tên môn học là bắt buộc
        ],[
            '*.0.required' => 'Tên môn học ở hàng :attribute là bắt buộc.',
        ])->validate();

        DB::beginTransaction();
        try {
            // Tạo tên chương trình đào tạo
            $curriculumName = 'Chương trình đào tạo';
            if ($this->school_year) {
                $curriculumName .= ' ' . $this->school_year;
            }
            if ($this->grade) {
                $curriculumName .= ' - Khối ' . $this->grade;
            }
            if ($this->subject_type) {
                $subjectTypeNames = [
                    'co_ban' => 'Cơ bản',
                    'chuyen_sau' => 'Chuyên sâu',
                    'tu_chon' => 'Tự chọn',
                    'bat_buoc' => 'Bắt buộc'
                ];
                $curriculumName .= ' - ' . ($subjectTypeNames[$this->subject_type] ?? $this->subject_type);
            }

            // Tìm hoặc tạo curriculum
            $curriculum = Curriculum::where('name', $curriculumName)
                ->where('department_id', $this->department_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$curriculum) {
                $curriculum = Curriculum::create([
                    'name' => $curriculumName,
                    'code' => 'CTDT-' . ($this->school_year ?? date('Y')) . '-' . ($this->grade ?? 'ALL'),
                    'department_id' => $this->department_id,
                    'description' => 'Chương trình đào tạo được import từ file Excel',
                    'user_id' => auth()->id(),
                ]);
            }

            // Thêm các chi tiết mới
            $details = [];
            $order = CurriculumDetail::where('curriculum_id', $curriculum->id)->max('order') ?? -1;
            
            foreach ($rows as $row) {
                foreach( $row as $k => $v ){
                    $row[$k] = trim($v ?? '');
                }
                
                if (empty($row[0])) {
                    continue;
                }

                $order++;
                $details[] = [
                    'curriculum_id' => $curriculum->id,
                    'subject_name' => $row[0], // Tên môn học
                    'credits' => (int)($row[1] ?? 0), // Số tín chỉ
                    'hours' => (int)($row[2] ?? 0), // Số giờ
                    'semester' => !empty($row[3]) ? (int)$row[3] : null, // Học kỳ
                    'note' => $row[4] ?? null, // Ghi chú
                    'order' => $order,
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

