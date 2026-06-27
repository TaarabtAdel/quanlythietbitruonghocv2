<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Http\Client\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class SgdPortalClient
{
    public function isConfigured(): bool
    {
        return $this->portalUrl() !== null && $this->apiKey() !== null;
    }

    public function portalUrl(): ?string
    {
        $url = Option::get_option('general', 'sgd_portal_url');

        return $url ? rtrim((string) $url, '/') : null;
    }

    public function apiKey(): ?string
    {
        $key = Option::get_option('general', 'sgd_api_key');

        return $key !== null && $key !== '' ? (string) $key : null;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateDocuments(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['limit'] ?? 20);
        $page = (int) ($filters['page'] ?? 1);

        if (! $this->isConfigured()) {
            return new LengthAwarePaginator([], 0, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        $response = $this->request('/api/documents', array_merge($filters, [
            'limit' => $perPage,
            'page' => $page,
        ]));

        if (! $response->ok()) {
            return new LengthAwarePaginator([], 0, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        $payload = $response->json('data') ?? [];
        $items = collect($payload['items'] ?? []);
        $pagination = $payload['pagination'] ?? [];

        return new LengthAwarePaginator(
            $items,
            (int) ($pagination['total'] ?? $items->count()),
            (int) ($pagination['per_page'] ?? $perPage),
            (int) ($pagination['current_page'] ?? $page),
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getDocument(int $id): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = $this->request("/api/documents/{$id}");

        if (! $response->ok()) {
            return null;
        }

        return $response->json('data');
    }

    /** @param  array<string, mixed>  $query */
    protected function request(string $path, array $query = []): Response
    {
        return Http::timeout(25)
            ->withHeaders(['X-SGD-Key' => $this->apiKey()])
            ->get($this->portalUrl().$path, $query);
    }
}
