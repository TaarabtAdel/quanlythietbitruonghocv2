<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $query = Document::orderBy('created_at','DESC');
        if($request->name){
            $query->where('name','LIKE','%'.$request->name.'%');
        }
        $items = $query->paginate($limit);
        $param = [
            'items' => $items,
        ];
        return view('teacher.documents.index', $param );
    }

    public function show($id, Request $request)
    {
        try {
            $item = Document::find($id);
            $params = [
                'item' => $item
            ];
            return view('teacher.documents.show', $params);
        } catch (ModelNotFoundException $e) {
            Log::error('Item not found: ' . $e->getMessage());
            return redirect()->route('teacher.documents.index' )->with('error', __('sys.item_not_found'));
        }
    }
}
