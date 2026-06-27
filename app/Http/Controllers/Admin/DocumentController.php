<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SgdPortalClient;
use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    protected $view_path    = 'admin.documents';
    protected $route_prefix = 'admin.documents.';
    protected $model        = Document::class;

    public function __construct(protected SgdPortalClient $sgdPortal) {}

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'internal');

        if ($tab === 'sgd') {
            $items = $this->sgdPortal->paginateDocuments([
                'name' => $request->name,
                'page' => $request->integer('page', 1),
                'limit' => 20,
            ]);

            return view($this->view_path.'.index', [
                'route_prefix' => $this->route_prefix,
                'model' => $this->model,
                'view_path' => $this->view_path,
                'tab' => 'sgd',
                'items' => $items,
                'sgdConfigured' => $this->sgdPortal->isConfigured(),
            ]);
        }

        $query = $this->model::query()
            ->where(function ($q) {
                $q->where('source', 'internal')->orWhereNull('source');
            })
            ->orderBy('created_at', 'DESC');

        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where('name', 'like', "%{$name}%");
        }

        if ($request->status > -1) {
            $request->status == 1 ? $query->whereNull('deleted_at') : $query->whereNotNull('deleted_at');
        }

        $items = $query->paginate(20)->appends($request->except(['_token', '_method']));

        return view($this->view_path.'.index', [
            'route_prefix' => $this->route_prefix,
            'model' => $this->model,
            'view_path' => $this->view_path,
            'tab' => 'internal',
            'items' => $items,
            'sgdConfigured' => $this->sgdPortal->isConfigured(),
        ]);
    }

    public function create()
    {
        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'item'          => new $this->model,
        ];

        return view($this->view_path.'.edit', $params);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = array_merge($request->except(['_token', '_method']), $validated);
        $data['source'] = 'internal';
        $data['deleted_at'] = $request->status == 1 ? null : now();
        unset($data['status']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->model::uploadFile($request->file('image'), $this->model::$upload_dir);
        }

        $this->model::create($data);

        return redirect()
            ->route($this->route_prefix.'index', ['tab' => 'internal'])
            ->with('success', 'Văn bản nội bộ đã được thêm.');
    }

    public function show(string $id)
    {
        $item = $this->model::findOrFail($id);

        return view($this->view_path.'.show', [
            'route_prefix' => $this->route_prefix,
            'model' => $this->model,
            'view_path' => $this->view_path,
            'item' => $item,
            'tab' => 'internal',
        ]);
    }

    public function sgdShow(int $sgdDocument)
    {
        $item = $this->sgdPortal->getDocument($sgdDocument);

        if (! $item) {
            return redirect()
                ->route($this->route_prefix.'index', ['tab' => 'sgd'])
                ->with('error', 'Không tải được văn bản từ Sở.');
        }

        return view($this->view_path.'.sgd-show', [
            'item' => $item,
            'route_prefix' => $this->route_prefix,
        ]);
    }

    public function edit(string $id)
    {
        $item = $this->model::findOrFail($id);
        $item->status = $item->deleted_at ? 0 : 1;

        return view($this->view_path.'.edit', [
            'route_prefix' => $this->route_prefix,
            'model' => $this->model,
            'view_path' => $this->view_path,
            'item' => $item,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $item = $this->model::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = array_merge($request->except(['_token', '_method']), $validated);
        $data['deleted_at'] = $request->status == 1 ? null : now();
        unset($data['status']);
        $item->update($data);

        return redirect()->back()->with('success', 'Văn bản đã được cập nhật.');
    }

    public function destroy(Request $request, string $id)
    {
        $item = $this->model::findOrFail($id);
        if ($item->deleted_at) {
            $item->delete();
        } else {
            $item->deleted_at = now();
            $item->save();
        }

        return redirect()
            ->route($this->route_prefix.'index', ['tab' => 'internal', 'page' => $request->page])
            ->with('success', 'Văn bản đã được xóa.');
    }
}
