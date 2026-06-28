<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Document::query()
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'DESC');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%'.$request->name.'%');
        }

        $items = $query->paginate($request->integer('limit', 20));

        return $this->paginated($items, fn (Document $item) => [
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'image' => Document::resolveImageUrl($item->image),
            'description' => $item->description,
            'created_at' => $item->created_at?->toIso8601String(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $item = Document::findOrFail($id);

            return $this->success([
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'image' => Document::resolveImageUrl($item->image),
                'description' => $item->description,
                'created_at' => $item->created_at?->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return $this->error(__('sys.item_not_found'), 404);
        }
    }
}
