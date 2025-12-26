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
        $query = $this->model::with('department')->whereNull('deleted_at')->orderBy('created_at','DESC');

        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where('name', 'like', "%{$name}%");
        }

        if ($request->filled('code')) {
            $code = trim($request->code);
            $query->where('code', 'like', "%{$code}%");
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
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
        $item = $this->model::with(['details', 'department', 'user'])->whereNull('deleted_at')->findOrFail($id);

        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'item'          => $item,
        ];

        return view($this->view_path.'.show', $params);
    }
}

