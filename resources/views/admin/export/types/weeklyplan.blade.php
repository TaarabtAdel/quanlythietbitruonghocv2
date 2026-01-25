<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Tuần : <span class="text-danger">(*)</span></label>
            <!-- <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control"
                value="{{ request()->week }}"> -->
            <x-form-input-school-week name="week" selected_id="{{ request()->week }}" />
            <x-form-input-error field="sw_start_week" />
        </div>
    </div>
    <div class="col-lg-6">
        <div id="preview-demo-img">
            <img class="img-fluid" src="/system/export/preview/weeklyplan.png" alt="">
        </div>
    </div>
</div>