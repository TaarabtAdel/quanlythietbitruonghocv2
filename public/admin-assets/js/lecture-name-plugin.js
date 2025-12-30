$(document).ready(function() {
    // 1. Khi nhấn vào input: Hiện dropdown của đúng input đó
    $(document).on('focus', '.curriculum-input', function() {
        $('.curriculum-dropdown').hide(); // Ẩn tất cả cái khác
        $(this).siblings('.curriculum-dropdown').show();
        
        // Nếu đã có đủ dữ liệu select từ trước, tự động load bài học
        checkAndLoadData($(this).siblings('.curriculum-dropdown'));
    });

    // 2. Click ra ngoài để đóng dropdown
    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('.curriculum-select-wrapper').length) {
            $('.curriculum-dropdown').hide();
        }
    });

    // 3. Khi thay đổi bất kỳ select nào trong dropdown
    $(document).on('change', '.sel-year, .sel-grade, .sel-subject, .sel-type', function() {
        let $dropdown = $(this).closest('.curriculum-dropdown');
        checkAndLoadData($dropdown);
    });

    // Hàm kiểm tra điều kiện và gọi AJAX
    function checkAndLoadData($dropdown) {
        let params = {
            academic_year: $dropdown.find('.sel-year').val(),
            grade: $dropdown.find('.sel-grade').val(),
            department_id: $dropdown.find('.sel-subject').val(),
            subject_type: $dropdown.find('.sel-type').val()
        };

        // Chỉ gọi AJAX khi cả 4 trường đã được chọn
        if (params.academic_year && params.grade && params.department_id && params.subject_type) {
            let $list = $dropdown.find('.list-items');
            $list.html('<li class="list-group-item text-center">Đang tải dữ liệu...</li>');

            $.ajax({
                url: '/curricula/get-lessons', // Thay bằng route của bạn
                method: 'GET',
                data: {
                    ...params,
                    // _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    let html = '';
                    if (res.length > 0) {
                        let prefix = (params.subject_type === 'chuyen_de') ? 'CD' : '';
                        res.forEach(item => {
                            let displayLessonNumber = prefix + item.lesson_number;

                            html += `<li class="list-group-item item-select" data-lesson_number="${displayLessonNumber}" data-lesson_name="${item.lesson_name}">
                                        <strong>${(params.subject_type === 'chuyen_de') ? 'CD' : 'T'}${item.lesson_number}:</strong> ${item.lesson_name}
                                     </li>`;
                        });
                    } else {
                        html = '<li class="list-group-item text-danger text-center">Không có bài học nào</li>';
                    }
                    $list.html(html);
                },
                error: function() {
                    $list.html('<li class="list-group-item text-danger">Lỗi tải dữ liệu</li>');
                }
            });
        }
    }

    // 4. Khi chọn bài học từ danh sách
    $(document).on('click', '.item-select', function() {
        let lesson_name = $(this).data('lesson_name');
        let lesson_number = $(this).data('lesson_number');
        let $wrapper = $(this).closest('.curriculum-select-wrapper');

        let targetName = $wrapper.find('.curriculum-input').data('target');
        $('#'+targetName).val(lesson_name); // Gán giá trị vào input
        $wrapper.find('.curriculum-input').val(lesson_number); // Gán giá trị vào input
        $wrapper.find('.curriculum-dropdown').hide(); // Đóng dropdown
    });
});