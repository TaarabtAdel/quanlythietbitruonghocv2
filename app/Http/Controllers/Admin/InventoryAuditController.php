<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryAudit;

class InventoryAuditController extends Controller
{
    protected $view_path    = 'admin.inventory_audits';
    protected $route_prefix = 'admin.inventory_audits';
    protected $model        = InventoryAudit::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->model::orderBy('created_at','DESC');

        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where('name', 'like', "%{$name}%");
        }

        if ($request->status) {
            $query->where('status',$request->status);
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
    public function create(Request $request)
    {
        try {
            $saved = $this->model::create([
                'name' => 'Kiểm kê ngày '.date('d/m/Y'),
                'user_id' => Auth::id(),
                'date' => date('Y-m-d'),
                'status' => $this->model::DRAFT
            ]);
            return redirect()->route($this->route_prefix.'.edit',$saved->id)->with('success', __('sys.store_item_success'));
        } catch (QueryException $e) {
            //Log::error('Error in store method: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.store_item_error'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
        ]);

        $data = array_merge($request->except(['_token', '_method']), $validated);
        $data['deleted_at'] = $request->status == 1 ? NULL : now();
        unset($data['status']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->model::uploadFile($request->file('image'), $this->model::$upload_dir);
        } 

        $item = $this->model::create($data);

        return redirect()
            ->route($this->route_prefix.'index')
            ->with('success', 'Mục đích mượn đã được thêm thành công.');
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
        ]);

        $data = array_merge($request->except(['_token', '_method']), $validated);
        $data['deleted_at'] = $request->status == 1 ? NULL : now();
        unset($data['status']);
        $item->update($data);

        return redirect()
            ->back()
            ->with('success', 'Mục đích mượn đã được cập nhật.');
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
            ->with('success', 'Mục đích mượn đã được xóa.');
    }
}
