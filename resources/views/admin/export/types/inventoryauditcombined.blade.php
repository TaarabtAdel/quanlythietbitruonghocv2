<div class="form-group mb-4">
    <label class="form-label fw-bold">Chọn năm gốc : <span class="text-danger">(*)</span></label>
    <x-form-input-school-years name="origin_year" selected_id="{{ request()->origin_year }}" id="origin_year"/>
    <x-form-input-error field="origin_year" />
</div>
@for ($i = 2; $i <= 5; $i++)
    <div class="form-group mb-4">
        <label class="form-label fw-bold">Năm thứ {{ $i }} :</label>
        <x-form-input-school-years 
            name="year_{{ $i }}" 
            selected_id="{{ request('year_' . $i) }}" 
            id="year_{{ $i }}"
        />
        <x-form-input-error field="year_{{ $i }}" />
    </div>
@endfor