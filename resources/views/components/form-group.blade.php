<select class="form-control dropdown-toggle" name="group_id">
    @if(!$group_id)
        <option value="" selected>--Vui lòng chọn nhóm quyền--</option>
    @endif
    @foreach($model::all() as $record)
    <option value="{{ $record->id }}" @selected($group_id == $record->id)>{{ $record->name }}</option>
    @endforeach
</select>