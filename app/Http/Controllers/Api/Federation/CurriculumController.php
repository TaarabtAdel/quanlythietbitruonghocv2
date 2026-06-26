<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Curriculum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 30);
        $query = Curriculum::query()
            ->with('department')
            ->withCount('details')
            ->whereNull('deleted_at')
            ->orderByDesc('created_at');

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        $items = $query->paginate($limit);

        return $this->paginated($items, fn (Curriculum $item) => [
            'id' => $item->id,
            'academic_year' => $item->academic_year,
            'department_id' => $item->department_id,
            'department_name' => $item->department?->name,
            'grade' => $item->grade,
            'grade_name' => $item->grade_name,
            'subject_type' => $item->subject_type,
            'subject_type_label' => $this->subjectTypeLabel($item->subject_type),
            'details_count' => $item->details_count,
            'status' => (int) $item->status,
            'status_label' => (int) $item->status === Curriculum::ACTIVE ? 'Hoạt động' : 'Ẩn',
            'note' => $item->note,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = Curriculum::with(['details', 'department'])
            ->whereNull('deleted_at')
            ->find($id);

        if (! $item) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        return $this->success([
            'id' => $item->id,
            'academic_year' => $item->academic_year,
            'department_id' => $item->department_id,
            'department_name' => $item->department?->name,
            'grade' => $item->grade,
            'grade_name' => $item->grade_name,
            'subject_type' => $item->subject_type,
            'subject_type_label' => $this->subjectTypeLabel($item->subject_type),
            'status' => (int) $item->status,
            'status_label' => (int) $item->status === Curriculum::ACTIVE ? 'Hoạt động' : 'Ẩn',
            'note' => $item->note,
            'details' => $item->details
                ->sortBy([['week', 'asc'], ['lesson_number', 'asc']])
                ->values()
                ->map(fn ($detail) => [
                    'id' => $detail->id,
                    'week' => $detail->week,
                    'lesson_number' => $detail->lesson_number,
                    'lesson_name' => $detail->lesson_name,
                    'note' => $detail->note,
                ]),
        ]);
    }

    protected function subjectTypeLabel(?string $type): string
    {
        return match ($type) {
            'mon_chinh' => 'Môn chính',
            'chuyen_de' => 'Chuyên đề',
            default => $type ?? '',
        };
    }
}
