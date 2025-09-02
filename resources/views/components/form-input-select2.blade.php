<select class="form-control select2" name="{{ $name }}" @if($autoSubmit) onchange="this.form.submit()" @endif>
    <option value="">---</option>
    @foreach( $items as $item )
    <option @selected($selected_id == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
    @endforeach
</select>