<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    protected $view_path    = 'teacher.documents';
    protected $route_prefix = 'documents.';
    protected $model        = Document::class;

    public function index(Request $request)
    {
        $query = $this->model::query()
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('source', 'internal')->orWhereNull('source');
            })
            ->orderBy('created_at', 'DESC');
        if ($request->name) {
            $query->where('name', 'LIKE', '%'.$request->name.'%');
        }
        $items = $query->paginate(20);

        return view($this->view_path.'.index', [
            'items' => $items,
            'route_prefix' => $this->route_prefix,
        ]);
    }

    public function show($id, Request $request)
    {
        try {
            $item = $this->model::query()
                ->whereNull('deleted_at')
                ->where(function ($q) {
                    $q->where('source', 'internal')->orWhereNull('source');
                })
                ->findOrFail($id);

            return view($this->view_path.'.show', ['item' => $item]);
        } catch (\Exception $e) {
            return redirect()->route($this->route_prefix.'index')->with('error', __('sys.item_not_found'));
        }
    }
}
