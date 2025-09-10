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
        $query = $this->model::whereNull('deleted_at')->orderBy('created_at','DESC');
        if($request->name){
            $query->where('name','LIKE','%'.$request->name.'%');
        }
        $items = $query->paginate(20);
        $param = [
            'items' => $items,
            'route_prefix' => $this->route_prefix,
        ];
        return view($this->view_path.'.index', $param );
    }

    public function show($id, Request $request)
    {
        try {
            $item = $this->model::findOrFail($id);
            $params = [
                'item' => $item
            ];
            return view($this->view_path.'.show', $params);
        } catch (\Exception $e) {
            return redirect()->route($this->route_prefix.'index' )->with('error', __('sys.item_not_found'));
        }
    }
}
