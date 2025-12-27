<select class="form-control" name="{{ $name }}" @if($autoSubmit) onchange="this.form.submit()" @endif @if( isset($id) ) id="{{ $id }}" @endif>
    <option value="">---</option>
    @foreach( $items as $item )
    <option @selected($selected_id == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
    @endforeach
</select>