{{-- File: form-input-school-week.blade.php --}}

<input
    type="text"
    id="{{ $id }}"
    {{-- Bỏ name ra khỏi input hiển thị để tránh bị submit 2 lần --}}
    {{-- name="{{ $name }}" --}} 
    class="form-control {{ $selected_id ? 'school-week-selected' : '' }}"
    placeholder="Chọn Năm học - Tuần học"
    value="{{ $selected_id }}" 
    readonly
>
<script>
    // Đảm bảo mã chạy sau khi DOM được tải hoàn toàn
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initSchoolWeekSelect === 'function') {
            
            const config = @json($schoolConfig);
            const defaultYear = '{{ $defaultYear }}';
            const inputId = '#{{ $id }}';
            const inputName = '{{ $name }}'; // Tên này sẽ dùng cho input hidden được tạo bởi JS
            const autoSubmit = {{ $autoSubmit ? 'true' : 'false' }};
            const selectedValue = '{{ $selected_id }}'; 
            let [selectedSchoolYear = '', rawWeek = ''] = (selectedValue ?? '').split('|').map(item => item.trim());
            if(!selectedSchoolYear){
                selectedSchoolYear = defaultYear;
            }
            const selectedSchoolWeek = rawWeek ? Number(rawWeek.replace(/\D/g, '')) : '';
            console.log('selectedSchoolYear',selectedSchoolYear);
            
            
            // Gọi hàm khởi tạo toàn cục từ plugin.js
            initSchoolWeekSelect(
                inputId, 
                config, 
                defaultYear, 
                inputName, 
                autoSubmit,
                selectedSchoolYear,
                selectedSchoolWeek
            );
            const popup_id = jQuery(inputId).data('popup-id');
            
            jQuery('body').find('#'+popup_id+' .custom-school-week-option[data-value="'+selectedSchoolWeek+'"]').addClass('custom-school-week-active');
            jQuery('body').find('.school-week-selected').closest('.sw-input-wrapper').find('.sw-clear-icon').show();
        } else {
            console.error('Lỗi: Hàm initSchoolWeekSelect không được tìm thấy. Vui lòng đảm bảo file school-week-plugin.js đã được nhúng và biên dịch.');
        }
    });
</script>