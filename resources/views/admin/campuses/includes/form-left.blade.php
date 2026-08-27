<div class="card">
    <div class="card-body">
        <div class="mb-4">
            <label class="mb-3">Tên cơ sở <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control" name="name" value="{{ $item->name ?? old('name') }}" placeholder="Ví dụ: Cơ sở 2 - Nguyễn Huệ">
            <x-form-input-error field="name"/>
        </div>
        <div class="mb-4">
            <label class="mb-3">Tên database</label>
            <div class="input-group">
                <span class="input-group-text">{{ $dbPrefix }}</span>
                <input type="text" class="form-control" name="database_name"
                    value="{{ old('database_name', $item->database_name ? \App\Services\CampusService::unqualifyDatabaseName($item->database_name) : '') }}"
                    placeholder="{{ \App\Support\TenantContext::schoolSlug() ?: 'cs' }}_ten_co_so">
            </div>
            <div class="form-text">
                Chỉ điền phần sau prefix, ví dụ <code>{{ \App\Support\TenantContext::schoolSlug() ?: 'cs' }}_ten_co_so</code>
                (hệ thống tự gán <code>{{ $dbPrefix }}</code>).
                Local/VPS: để trống hoặc nhập tên, hệ thống tự <code>CREATE DATABASE</code> rồi clone bảng.
                cPanel: thường phải tạo DB sẵn trong <strong>MySQL Databases</strong>, gán user website vào DB, rồi thêm cơ sở — hệ thống sẽ clone bảng vào DB đó.
            </div>
            <x-form-input-error field="database_name"/>
        </div>
        <div class="mb-4">
            <label class="mb-3">Thứ tự</label>
            <input type="number" min="0" class="form-control" name="sort_order" value="{{ $item->sort_order ?? old('sort_order', 0) }}">
        </div>
    </div>
</div>
