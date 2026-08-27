<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Services\CampusService;
use App\Support\TenantContext;
use App\Support\TenantDatabase;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    protected $view_path = 'admin.campuses';

    protected $route_prefix = 'admin.campuses.';

    protected $model = Campus::class;

    public function index(Request $request)
    {
        $this->authorizeMainAdmin();
        Campus::ensureSchema();

        $query = $this->model::orderBy('sort_order')->orderBy('name');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.trim($request->name).'%');
        }

        if ($request->status > -1) {
            $request->status == 1
                ? $query->whereNull('deleted_at')
                : $query->whereNotNull('deleted_at');
        }

        $items = $query->paginate(20)->appends($request->except(['_token', '_method']));

        return view($this->view_path.'.index', [
            'route_prefix' => $this->route_prefix,
            'model' => $this->model,
            'view_path' => $this->view_path,
            'items' => $items,
            'mainDatabase' => TenantContext::mainDatabase(),
            'dbPrefix' => config('tenant.database_prefix'),
        ]);
    }

    public function create()
    {
        $this->authorizeMainAdmin();
        return view($this->view_path.'.edit', [
            'route_prefix' => $this->route_prefix,
            'model' => $this->model,
            'view_path' => $this->view_path,
            'item' => new $this->model,
            'mainDatabase' => TenantContext::mainDatabase(),
            'dbPrefix' => config('tenant.database_prefix'),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeMainAdmin();
        Campus::ensureSchema();
        $validated = $this->validated($request);

        try {
            CampusService::provisionDatabase($validated['database_name'], $validated['name']);
        } catch (\Throwable $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'database_name' => $e->getMessage(),
            ]);
        }

        $data = $validated;
        $data['deleted_at'] = $request->status == 1 ? null : now();
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $this->model::create($data);

        return redirect()
            ->route($this->route_prefix.'index')
            ->with('success', 'Đã thêm cơ sở. Có thể chuyển sang cơ sở này từ menu phía trên để xem dữ liệu.');
    }

    public function edit(string $id)
    {
        $this->authorizeMainAdmin();
        $item = $this->model::findOrFail($id);
        $item->status = $item->deleted_at ? 0 : 1;

        return view($this->view_path.'.edit', [
            'route_prefix' => $this->route_prefix,
            'model' => $this->model,
            'view_path' => $this->view_path,
            'item' => $item,
            'mainDatabase' => TenantContext::mainDatabase(),
            'dbPrefix' => config('tenant.database_prefix'),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeMainAdmin();
        $item = $this->model::findOrFail($id);
        $validated = $this->validated($request, $item->id);

        $data = $validated;
        $data['deleted_at'] = $request->status == 1 ? null : now();

        $item->update($data);

        return redirect()
            ->back()
            ->with('success', 'Đã cập nhật cơ sở.');
    }

    public function destroy(Request $request, string $id)
    {
        $this->authorizeMainAdmin();
        $item = $this->model::findOrFail($id);
        if ($item->deleted_at) {
            $item->delete();
        } else {
            $item->deleted_at = now();
            $item->save();
        }

        return redirect()
            ->route($this->route_prefix.'index', ['page' => $request->page])
            ->with('success', 'Đã xóa cơ sở.');
    }

    public function switch(Request $request)
    {
        abort_unless(CampusService::isMainAdmin(), 403, 'Chỉ cơ sở chính được xem các cơ sở khác.');

        $validated = $request->validate([
            'campus_key' => ['required', 'string'],
        ]);

        $error = CampusService::connectTo($validated['campus_key']);
        if ($error) {
            return back()->with('error', $error);
        }

        CampusService::putSessionKey($validated['campus_key']);

        $name = TenantContext::campusName() ?: 'cơ sở đã chọn';

        return redirect('/admin')->with('success', 'Đang xem: '.$name);
    }

    protected function authorizeMainAdmin(): void
    {
        abort_unless(CampusService::canManageCampuses(), 403, 'Chỉ tài khoản cơ sở chính được quản lý cơ sở trực thuộc.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        if (! $request->filled('database_name')) {
            $request->merge(['database_name' => null]);
        }

        $mainDb = TenantContext::mainDatabase();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'database_name' => [
                'nullable',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9_]+$/',
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'name.required' => 'Nhập tên cơ sở.',
            'database_name.regex' => 'Tên database chỉ gồm chữ, số và gạch dưới.',
        ]);

        $data['database_name'] = trim((string) ($data['database_name'] ?? ''));
        if ($data['database_name'] === '') {
            $data['database_name'] = CampusService::suggestedDatabaseName($data['name']);
        } else {
            $data['database_name'] = CampusService::qualifyDatabaseName($data['database_name']);
        }

        if (strlen($data['database_name']) > 64) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'database_name' => 'Tên database quá dài (tối đa 64 ký tự sau khi thêm prefix).',
            ]);
        }

        $taken = $this->model::query()
            ->where('database_name', $data['database_name'])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($taken) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'database_name' => 'Database này đã được gán cho cơ sở khác. Đổi tên cơ sở hoặc nhập tên database khác.',
            ]);
        }

        if ($mainDb && strcasecmp($data['database_name'], $mainDb) === 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'database_name' => 'Đây là database của trường (cơ sở chính), không cần thêm.',
            ]);
        }

        return $data;
    }
}
