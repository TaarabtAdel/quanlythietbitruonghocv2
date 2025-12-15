<div class="row">
    <div class="col-lg-6">
        <style>
        .select2-container {
            z-index: 99999;
        }
        </style>

        <div class="form-group mb-4">
            <label class="form-label fw-bold">Chọn năm gốc : <span class="text-danger">(*)</span></label>
            <select name="origin_year" class="form-control select2">
                <option value="">---</option>
                @foreach( \App\Models\InventoryAudit::where('status',1)->get() as $inventoryAudit )
                <option value="{{ $inventoryAudit->id }}">
                    {{ $inventoryAudit->name }}
                </option>
                @endforeach
            </select>
            <x-form-input-error field="origin_year" />
        </div>

        @for ($i = 2; $i <= 5; $i++) 
            <div class="form-group mb-4">
                <label class="form-label fw-bold">Năm thứ {{ $i }} :</label>
                <select name="year_{{ $i }}" id="year_{{ $i }}" class="form-control select2">
                    <option value="">---</option>
                    @foreach( \App\Models\InventoryAudit::where('status',1)->get() as $inventoryAudit )
                    <option value="{{ $inventoryAudit->id }}">
                        {{ $inventoryAudit->name }}
                    </option>
                    @endforeach
                </select>
                <x-form-input-error field="year_{{ $i }}" />
            </div>
        @endfor
    </div>

    <div class="col-lg-6">
        <div id="preview-demo-img">
            <img class="img-fluid" src="/system/export/preview/inventoryauditcombined.png" alt="">
        </div>
    </div>
</div>