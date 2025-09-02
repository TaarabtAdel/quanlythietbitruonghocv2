<nav class="float-end">
    {{ $items->appends(request()->query())->links() }}
</nav>