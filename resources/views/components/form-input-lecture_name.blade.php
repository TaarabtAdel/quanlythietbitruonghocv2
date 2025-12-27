<div class="curriculum-select-wrapper">
    <input type="text" 
        class="form-control curriculum-input" 
        id="devices_{{ $tiet }}_{{ $name }}" 
        data-name="name_1" 
        name="devices[{{ $tiet }}][{{ $name }}]"
        value="{{ $value }}"
        data-target="{{ $target }}"
        placeholder="Nhấn để chọn bài học..."
        autocomplete="off"
    >
    <div class="curriculum-dropdown">
        <div class="dropdown-filters">
            <select class="form-select sel-year">
                <option value="">-- Năm học --</option>
                @foreach($schoolYears as $schoolYear)
                    <option value="{{ $schoolYear->name }}" {{ $schoolYear->id == $defaultYear ? 'selected' : '' }}>{{ $schoolYear->name }}</option>
                @endforeach
            </select>
            <select class="form-select sel-grade">
                <option value="">-- Khối --</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ $grade->id == $defaultGrade ? 'selected' : '' }}>{{ $grade->name }}</option>
                @endforeach
            </select>
            <select class="form-select sel-subject">
                <option value="">-- Môn học --</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ $s->id == $defaultSubject ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            <select class="form-select sel-type">
                <option value="">-- Phân môn --</option>
                @foreach($subjectTypes as $key => $type)
                    <option value="{{ $key }}" {{ $key == $defaultSubjectType ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <ul class="list-group list-items">
            <li class="list-group-item text-muted text-center">Vui lòng chọn đủ thông tin</li>
        </ul>
    </div>
</div>