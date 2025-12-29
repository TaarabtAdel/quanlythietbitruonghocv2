<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Curriculum;
use App\Models\CurriculumDetail;

class CurriculumController extends Controller
{
    protected $view_path    = 'admin.curricula';
    protected $route_prefix = 'admin.curricula.';
    protected $model        = Curriculum::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->model::with('department')->withCount('details')->orderBy('created_at','DESC');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        $items = $query->paginate(20)->appends($request->except(['_token', '_method']));

        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'items'         => $items,
        ];

        return view($this->view_path.'.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'item'          => new $this->model
        ];
        return view($this->view_path.'.edit', $params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'grade'        => 'nullable|string',
            'subject_type' => 'nullable|string',
            'note'         => 'nullable|string',
            'status'       => 'required|in:'.Curriculum::ACTIVE.','.Curriculum::INACTIVE,
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;
            $curriculum = $this->model::create($data);

            // Lưu các chi tiết
            if ($request->has('details') && is_array($request->details)) {
                $details = [];
                foreach ($request->details as $index => $detail) {
                    if (!empty($detail['lesson_name'])) {
                        $details[] = [
                            'curriculum_id' => $curriculum->id,
                            'week' => !empty($detail['week']) ? (int)$detail['week'] : null,
                            'lesson_number' => !empty($detail['lesson_number']) ? (int)$detail['lesson_number'] : null,
                            'lesson_name' => $detail['lesson_name'],
                            'note' => $detail['note'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($details)) {
                    CurriculumDetail::insert($details);
                }
            }

            DB::commit();
            return redirect()
                ->route($this->route_prefix.'index')
                ->with('success', 'Chương trình đào tạo đã được thêm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = $this->model::with(['details', 'department'])->findOrFail($id);

        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'item'          => $item,
        ];

        return view($this->view_path.'.show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = $this->model::with(['details', 'department'])->findOrFail($id);

        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'item'          => $item,
        ];
        return view($this->view_path.'.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = $this->model::findOrFail($id);

        $validated = $request->validate([
            'academic_year' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'grade'        => 'nullable|string',
            'subject_type' => 'nullable|string',
            'note'         => 'nullable|string',
            'status'       => 'required|in:'.Curriculum::ACTIVE.','.Curriculum::INACTIVE,
        ]);

        DB::beginTransaction();
        try {
            $item->update($validated);

            // Xóa tất cả chi tiết cũ và tạo lại
            CurriculumDetail::where('curriculum_id', $id)->delete();

            // Lưu các chi tiết mới
            if ($request->has('details') && is_array($request->details)) {
                $details = [];
                foreach ($request->details as $index => $detail) {
                    if (!empty($detail['lesson_name'])) {
                        $details[] = [
                            'curriculum_id' => $id,
                            'week' => !empty($detail['week']) ? (int)$detail['week'] : null,
                            'lesson_number' => !empty($detail['lesson_number']) ? (int)$detail['lesson_number'] : null,
                            'lesson_name' => $detail['lesson_name'],
                            'note' => $detail['note'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($details)) {
                    CurriculumDetail::insert($details);
                }
            }

            DB::commit();
            return redirect()
                ->back()
                ->with('success', 'Chương trình đào tạo đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $item = $this->model::findOrFail($id);
        $item->delete();
        return redirect()
            ->route($this->route_prefix.'index', ['page' => $request->page])
            ->with('success', 'Chương trình đào tạo đã được xóa.');
    }

    public function copy(Request $request, string $id)
    {
        $item = $this->model::findOrFail($id);
        $this->model::copyItem($id);
        return redirect()->route($this->route_prefix.'index')->with('success', __('sys.copy_item_success'));
    }
}
