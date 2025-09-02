<select class="form-control dropdown-toggle" name="nest_id">
    @if(!$nest_id)
        <option value="" selected>--Vui lòng chọn tổ--</option>
    @endif
    @foreach($model::all() as $record)
    <option value="{{ $record->id }}" @selected($nest_id == $record->id)>{{ $record->name }}</option>
    @endforeach
</select>