(function ($) {
    
    // --- KHỐI HÀM TIỆN ÍCH ĐÃ ĐƯỢC ĐÓNG GÓI (UTILS) ---
    const sw_Utils = { 
        
        addDays: function(date, days) {
            var result = new Date(date);
            result.setDate(result.getDate() + days);
            return result;
        },

        // Định dạng hiển thị trên UI: DD/MM
        formatDate: function(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1;
            return (day < 10 ? '0' + day : day) + '/' + (month < 10 ? '0' + month : month);
        },

        // Định dạng cho query value (ISO): YYYY-MM-DD
        formatDateToISO: function(date) {
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();
            return year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);
        },
        
        // Chấp nhận định dạng YYYY-MM-DD từ config
        parseDate: function(dateStr) {
            var parts = dateStr.split('-');
            return new Date(parts[0], parts[1] - 1, parts[2]);
        },
        
        /**
         * Hàm sinh danh sách tuần. Đảm bảo Tuần 1 bắt đầu từ Thứ Hai gần nhất.
         */
        generateWeeks: function(config,year = null) {
            if (!config || !config.startWeek1 || config.numberWeek <= 0) return [];
            
            const startDateStr = config.startWeek1;
            const totalWeeks = config.numberWeek;

            const numWeeks = Math.min(totalWeeks, 40); 
            var weeks = [];
            
            // 1. Phân tích ngày cấu hình (YYYY-MM-DD)
            var configuredStartDate = sw_Utils.parseDate(startDateStr); 
            
            // 2. TÌM THỨ HAI GẦN NHẤT: Đảm bảo Week 1 bắt đầu từ Thứ Hai (Monday = 1)
            var dayOfWeek = configuredStartDate.getDay(); // 0 (Sun) to 6 (Sat)
            var daysToShiftBack = (dayOfWeek + 6) % 7; 
            
            // Ngày bắt đầu thực tế của TUẦN 1 (Thứ Hai trước ngày cấu hình)
            var week1StartDate = sw_Utils.addDays(configuredStartDate, -daysToShiftBack); 
            
            var currentWeekStart = week1StartDate; 
            
            for (var i = 0; i < numWeeks; i++) {
                
                var currentEnd = sw_Utils.addDays(currentWeekStart, 6); // Kết thúc Thứ Bảy
                
                var type = i + 1 <= 20 ? 'hk1' : 'hk2';

                weeks.push({
                    week: i + 1,
                    type: type, 
                    year: year, 
                    start: sw_Utils.formatDate(currentWeekStart), 
                    end: sw_Utils.formatDate(currentEnd),
                    startISO: sw_Utils.formatDateToISO(currentWeekStart), 
                    endISO: sw_Utils.formatDateToISO(currentEnd)
                });
                
                currentWeekStart = sw_Utils.addDays(currentWeekStart, 7);
            }
            return weeks;
        }
    };
    // --------------------------------------------------------

    // --- CÁC HÀM HỖ TRỢ CỦA PLUGIN ---

    function sw_createPopup($input, settings) {
        
        if ($input.data('popup-id')) {
            $('#' + $input.data('popup-id')).remove();
        }

        var popupId = 'custom-school-week-popup-' + Math.random().toString(36).substr(2, 9);
        $input.attr('data-popup-id', popupId);

        var $popup = $('<div class="custom-school-week-container" id="' + popupId + '"></div>');

        var $yearSelect = $('<select class="custom-school-week-year-select"></select>');
        var years = Object.keys(settings.schoolConfig); 
        $.each(years, function(i, year) {
            let selected = null;
            if( settings.selectedSchoolYear == year ){
                selected = 'selected';
            }
            $yearSelect.append('<option '+selected+' value="' + year + '">' + year + '</option>');
        });
        
        if (settings.selectedSchoolYear) {
            $yearSelect.val(settings.selectedSchoolYear);
        }

        var $row1 = $('<div class="sw-row-year"></div>').append($('<label style="display:none;">Năm học:</label>'), $yearSelect);
        var $row2 = $('<div class="custom-school-week-list-title">**Chọn Tuần:**</div>');
        var $weeksList = $('<ul class="custom-school-week-weeks-list"></ul>');
        
        $popup.append($row1, $row2, $weeksList); 
        $('body').append($popup);
        
        return $popup;
    }

    function sw_updateWeeksList($popup, allWeeks) {
        var $weeksList = $popup.find('.custom-school-week-weeks-list').empty();
        
        var displayWeeks = allWeeks;

        if (displayWeeks.length === 0) {
            $weeksList.append('<li class="custom-school-week-no-data">Chưa cấu hình tuần học cho năm này.</li>');
        }
        
        $.each(displayWeeks, function(i, w) {
            var $li = $('<li data-value="' + w.week + '" class="custom-school-week-option"></li>').html(
                'Tuần ' + w.week + ' (' + w.start + ' - ' + w.end + ')'
            ).data({
                'week-number': w.week,
                'start-date': w.start, 
                'end-date': w.end,     
                'start-iso': w.startISO, 
                'end-iso': w.endISO,     
                'week-type': w.type 
            });
            $weeksList.append($li);
        });
    }

    function sw_positionPopup($input, $popup) {
        var offset = $input.offset();
        $popup.css({
            left: offset.left,
            top: offset.top + $input.outerHeight()
        });
    }

    function sw_showPopup($input, $popup) {
        sw_positionPopup($input, $popup);
        $popup.show();
    }
    
    /**
     * Xóa tất cả các lựa chọn và giá trị input (Reset 2 trường Start/End Date)
     */
    function sw_clearSelection($input, name, $popup) {
        $input.val(''); 
        $input.removeClass('school-week-selected'); 
        $input.data('full-value', null); 
        
        // --- KHẮC PHỤC: TRUY VẤN CỤC BỘ VÀ XÓA GIÁ TRỊ DỰA TRÊN TÊN ĐỘNG ---
        const $wrapper = $input.closest('.sw-input-wrapper');

        $wrapper.find('input[name="sw_start_' + name + '"]').val('');
        $wrapper.find('input[name="sw_end_' + name + '"]').val('');
        // ----------------------------------------------------
        
        if ($popup && $popup.length) {
            $popup.find('.custom-school-week-option').removeClass('custom-school-week-active');
            $popup.hide(); 
        }

        $input.trigger('change');
        if( $input.hasClass('autoSubmit') ){
            $input.closest('form').trigger('submit');
        }
    }

    /**
     * Cập nhật giá trị Input và Hidden Field (Set giá trị cho 2 trường Start/End Date)
     */
    function sw_updateInputValue($input, $popup, $selectedWeek, name) {
        var year = $popup.find('.custom-school-week-year-select').val(); 
        
        var weekNum = $selectedWeek.data('week-number'); 
        var startDate = $selectedWeek.data('start-date'); 
        var endDate = $selectedWeek.data('end-date');     
        var startISO = $selectedWeek.data('start-iso');   
        var endISO = $selectedWeek.data('end-iso');       
             
        
        var paddedWeekNum = weekNum.toString().padStart(2, '0');
        
        // --- KHẮC PHỤC: TRUY VẤN CỤC BỘ VÀ SET GIÁ TRỊ DỰA TRÊN TÊN ĐỘNG ---
        const $wrapper = $input.closest('.sw-input-wrapper');
        // const valueText = 'Năm: ' + year + ' | Tuần ' + paddedWeekNum + ' (' + startDate + ' - ' + endDate + ')';
        const valueText = year + ' | Tuần ' + paddedWeekNum;

        $wrapper.find('input[name="sw_start_' + name + '"]').val(startISO);
        $wrapper.find('input[name="sw_end_' + name + '"]').val(endISO);
        $wrapper.find('input[name="sw_display_' + name + '"]').val(valueText);
        // ----------------------------------------------------
        
        // Giá trị hiển thị trên input
        $input.val(valueText);
             
        $input.data('full-value', {
            year: year,
            week: weekNum, 
            start: startDate,
            end: endDate
        });

        // Áp dụng class để in đậm input và hiển thị icon xóa
        $input.addClass('school-week-selected');
        
        $input.trigger('change');
    }


    // --- HÀM CHÍNH CỦA PLUGIN ---
    $.fn.selectPopup = function (options) {
        var settings = $.extend({
            schoolConfig: {},
            defaultSchoolYear: null,
            selectedSchoolYear: null,
            selectedSchoolWeek:null,
            showOnEmpty: true,
            name: 'school_week', 
            autoSubmit: false
        }, options);

        var allWeeksData = {}; 
        
        function sw_calculateAndCacheWeeks(year) {
            const config = settings.schoolConfig[year];
            
            if (config && !allWeeksData[year]) {
                allWeeksData[year] = sw_Utils.generateWeeks(config,year); 
            }
            return allWeeksData[year] || [];
        }


        return this.each(function () {
            var $input = $(this);
            var $popup = sw_createPopup($input, settings); 
            
            // 1. Tải tuần ban đầu
            var initialYear = $popup.find('.custom-school-week-year-select').val();
            var initialAllWeeks = sw_calculateAndCacheWeeks(initialYear);
            sw_updateWeeksList($popup, initialAllWeeks); 
            
            // 2. Xử lý sự kiện thay đổi Năm học
            $popup.on('change', '.custom-school-week-year-select', function() {
                var selectedYear = $popup.find('.custom-school-week-year-select').val();
                
                sw_calculateAndCacheWeeks(selectedYear);
                
                var weeks = allWeeksData[selectedYear] || [];
                sw_updateWeeksList($popup, weeks); 
            });

            // 3. Xử lý hiển thị popup và đóng popup
            $input.on('click focus', function () {
                sw_showPopup($input, $popup); 
            });
            $(window).on('scroll resize', function () {
                if ($popup.is(':visible')) {
                    sw_positionPopup($input, $popup); 
                }
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest($input).length && !$(e.target).closest($popup).length && !$(e.target).closest('.sw-clear-icon').length) {
                    $popup.hide();
                }
            });

            // Xử lý nếu người dùng xóa nội dung input bằng tay
            $input.on('input', function() {
                if ($input.val() === '') {
                    sw_clearSelection($input, settings.name, $popup);
                }
            });
            
            // 4. Xử lý sự kiện chọn Tuần
            $popup.on('click', '.custom-school-week-option', function() {
                $popup.find('.custom-school-week-option').removeClass('custom-school-week-active');
                $(this).addClass('custom-school-week-active');
                sw_updateInputValue($input, $popup, $(this), settings.name); 
                $popup.hide(); 
                
                if (settings.autoSubmit) {
                     $input.closest('form').submit();
                }
            });
            
            // 5. Tự động mở popup nếu input rỗng
            if (settings.showOnEmpty && $input.val() === '') {
                 setTimeout(function() {
                    sw_showPopup($input, $popup); 
                 }, 50); 
            }
        });
    };
    
    // --- TẠO HÀM KHỞI TẠO TOÀN CỤC ---
    window.initSchoolWeekSelect = function(selector, config, defaultYear, name, autoSubmit, selectedSchoolYear,selectedSchoolWeek) {
        
        const $input = $(selector);
        if( autoSubmit ){
            $input.addClass('autoSubmit');
        }
        
        // 1. TẠO CÁC PHẦN TỬ HỖ TRỢ (Wrapper, Hidden Input Start/End, Clear Icon)
        
        // Kiểm tra sự tồn tại dựa trên tên động
        if ($('input[name="sw_start_' + name + '"]').length === 0) {
            
            // Quan trọng: Xóa name khỏi input hiển thị 
            if ($input.attr('name')) {
                 $input.removeAttr('name'); 
            }
            
            const $wrapper = $('<div class="sw-input-wrapper"></div>');
            const $clearIcon = $('<span class="sw-clear-icon" title="Xóa lựa chọn">×</span>');

            // Bọc input gốc, thêm input hidden và icon
            $input.wrap($wrapper); 
            $input.after(
                // Input ẩn cho START DATE
                $('<input>').attr({
                    type: 'hidden',
                    name: 'sw_start_' + name, // TÊN ĐỘNG
                    value: '' 
                }),
                // Input ẩn cho END DATE
                $('<input>').attr({
                    type: 'hidden',
                    name: 'sw_end_' + name, // TÊN ĐỘNG
                    value: ''
                }),
                // Input ẩn cho END DATE
                $('<input>').attr({
                    type: 'hidden',
                    name: 'sw_display_' + name, // TÊN ĐỘNG
                    value: ''
                }),
                $clearIcon
            );
        }

        // 2. Khởi tạo plugin
        $input.selectPopup({
            schoolConfig: config,
            defaultSchoolYear: defaultYear,
            selectedSchoolYear: selectedSchoolYear,
            selectedSchoolWeek: selectedSchoolWeek,
            name: name, 
            autoSubmit: autoSubmit,
            showOnEmpty: false
        });
        
        // 3. GÁN SỰ KIỆN XÓA cho nút icon mới
        const $clearIcon = $input.closest('.sw-input-wrapper').find('.sw-clear-icon');
        const $popup = $input.data('popup-id') ? $('#' + $input.data('popup-id')) : null;

        $clearIcon.on('click', function() {
            sw_clearSelection($input, name, $popup); 
        });

        // 4. Khôi phục trạng thái in đậm
        // if (selectedValue) {
        //     $input.addClass('school-week-selected');
        // }
    }

})(jQuery);