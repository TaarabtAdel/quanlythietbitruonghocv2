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
        $query = $this->model::with('department')->orderBy('created_at','DESC');

        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where('name', 'like', "%{$name}%");
        }

        if ($request->filled('code')) {
            $code = trim($request->code);
            $query->where('code', 'like', "%{$code}%");
        }

        if ($request->status > -1) {
            $request->status == 1 ? $query->whereNull('deleted_at') : $query->whereNotNull('deleted_at');
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
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'department_id'   => 'nullable|exists:departments,id',
        ]);

        DB::beginTransaction();
        try {
            $data = array_merge($request->except(['_token', '_method', 'details']), $validated);
            $data['deleted_at'] = $request->status == 1 ? NULL : now();
            $data['user_id'] = auth()->id();
            unset($data['status']);
            
            $curriculum = $this->model::create($data);

            // Lưu các chi tiết
            if ($request->has('details') && is_array($request->details)) {
                $details = [];
                foreach ($request->details as $index => $detail) {
                    if (!empty($detail['subject_name'])) {
                        $details[] = [
                            'curriculum_id' => $curriculum->id,
                            'subject_name' => $detail['subject_name'],
                            'credits' => $detail['credits'] ?? 0,
                            'hours' => $detail['hours'] ?? 0,
                            'semester' => $detail['semester'] ?? null,
                            'order' => $index,
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
        $item = $this->model::with(['details', 'department', 'user'])->findOrFail($id);

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
        $item = $this->model::with('details')->findOrFail($id);
        $item->status = $item->deleted_at ? 0 : 1;

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
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'department_id'   => 'nullable|exists:departments,id',
        ]);

        DB::beginTransaction();
        try {
            $data = array_merge($request->except(['_token', '_method', 'details']), $validated);
            $data['deleted_at'] = $request->status == 1 ? NULL : now();
            unset($data['status']);
            $item->update($data);

            // Xóa tất cả chi tiết cũ và tạo lại
            CurriculumDetail::where('curriculum_id', $id)->delete();

            // Lưu các chi tiết mới
            if ($request->has('details') && is_array($request->details)) {
                $details = [];
                foreach ($request->details as $index => $detail) {
                    if (!empty($detail['subject_name'])) {
                        $details[] = [
                            'curriculum_id' => $id,
                            'subject_name' => $detail['subject_name'],
                            'credits' => $detail['credits'] ?? 0,
                            'hours' => $detail['hours'] ?? 0,
                            'semester' => $detail['semester'] ?? null,
                            'order' => $index,
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
        if($item->deleted_at){
            $item->delete();
        }else{
            $item->deleted_at = now();
            $item->save();
        }
        return redirect()
            ->route($this->route_prefix.'index', ['page' => $request->page])
            ->with('success', 'Chương trình đào tạo đã được xóa.');
    }
}

