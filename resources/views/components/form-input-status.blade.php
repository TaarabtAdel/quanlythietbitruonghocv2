<select class="form-control dropdown-toggle" name="{{ $name }}" @if($autoSubmit) onchange="this.form.submit()" @endif>
    <option value="">--</option>
    <option value="0" @selected( @$status == 0 )>{{ __('sys.inactive') }}</option>
    <option value="1" @selected( @$status == 1 )>{{ __('sys.active') }}</option>
</select>