<div class="modal fade" id="modal-devices" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title fw-bold">DANH SÁCH THIẾT BỊ HIỆN CÓ</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body device-table-results">
                <div class="text-center pt-5 pb-5">{{ __('sys.loading_data') }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script>
    
    jQuery(document).ready(function() {
        let indexUrl = "{{ route('devices.index') }}";
        let positionUrl = "";
        let params = <?= json_encode(request()->query()); ?>;
        let wrapperResults = '.device-table-results';
        // Get all items
        getAjaxTable(indexUrl, wrapperResults, positionUrl, 'limit=20');

        // Handle pagination
        jQuery('body').on('click', wrapperResults + ' .page-link', function(e) {
            e.preventDefault();
            let url = jQuery(this).attr('href');
            getAjaxTable(url, wrapperResults, positionUrl);
        });
        jQuery('body').on('change', wrapperResults + ' .f-filter', function() {
            let filterData = jQuery(wrapperResults).find('.form-search').serialize();
            getAjaxTable(indexUrl, wrapperResults, positionUrl, filterData);
        });
        jQuery('body').on('keyup', wrapperResults + ' .f-filter-up',delay(function (e) {
            let filterData = jQuery(wrapperResults).find('.form-search').serialize();
            getAjaxTable(indexUrl, wrapperResults, positionUrl, filterData);
        }, 500));
    });
</script>