<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curriculum;

class CurriculumController extends Controller
{
    protected $view_path    = 'teacher.curricula';
    protected $route_prefix = 'curricula.';
    protected $model        = Curriculum::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->model::with('department')->withCount('details')->orderBy('created_at','DESC');

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
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
}

