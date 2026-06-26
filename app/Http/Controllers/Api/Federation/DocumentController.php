<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 30);
        $query = Document::query()
            ->whereNull('deleted_at')
            ->orderByDesc('created_at');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%'.$request->name.'%');
        }

        $items = $query->paginate($limit);

        return $this->paginated($items, fn (Document $item) => [
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'image' => $item->image,
            'description' => $item->description,
            'created_at' => $item->created_at?->toIso8601String(),
            'created_at_formatted' => $item->created_at?->format('d/m/Y H:i'),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = Document::query()->whereNull('deleted_at')->find($id);

        if (! $item) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        return $this->success([
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'image' => $item->image,
            'description' => $item->description,
            'created_at' => $item->created_at?->toIso8601String(),
            'created_at_formatted' => $item->created_at?->format('d/m/Y H:i'),
            'updated_at_formatted' => $item->updated_at?->format('d/m/Y H:i'),
        ]);
    }
}
