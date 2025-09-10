<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    protected $view_path    = 'admin.assets';
    protected $route_prefix = 'admin.assets.';
    protected $model        = Asset::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->model::orderBy('created_at','DESC');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('device_type_id')) {
            $query->where('device_type_id', $request->device_type_id);
        }

        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where('name', 'like', "%{$name}%");
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
            'department_id'   => 'required|integer',
            'device_type_id'  => 'required|integer',
        ]);

        $data = array_merge($request->except(['_token', '_method']), $validated);
        $data['deleted_at'] = $request->status == 1 ? NULL : now();
        unset($data['status']);
        $item = $this->model::create($data);

        return redirect()
            ->route($this->route_prefix.'index')
            ->with('success', 'Tài sản đã được thêm thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = $this->model::findOrFail($id);

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
        $item = $this->model::findOrFail($id);
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
            'department_id'   => 'required|integer',
            'device_type_id'  => 'required|integer',
        ]);

        $data = array_merge($request->except(['_token', '_method']), $validated);
        $data['deleted_at'] = $request->status == 1 ? NULL : now();
        unset($data['status']);
        $item->update($data);

        return redirect()
            ->back()
            ->with('success', 'Tài sản đã được cập nhật.');
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
            ->with('success', 'Tài sản đã được xóa.');
    }
}
