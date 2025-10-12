
$(function () {
	"use strict";


	// app dropdown
	// new PerfectScrollbar(".app-container")
	new PerfectScrollbar(".header-notifications-list")


	$(".sidebar-close").on("click", function () {
		$("body").removeClass("toggled")
	})



	$(".dark-mode span").click(function () {
		$(this).text(function (i, v) {
			return v === 'dark_mode' ? 'light_mode' : 'dark_mode'
		})
	});



	$(function () {
		$("#menu").metisMenu()
	})


	$(".btn-toggle-menu").click(function () {
		$("body").hasClass("toggled") ? ($("body").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($("body").addClass("toggled"), $(".sidebar-wrapper").hover(function () {
			$("body").addClass("sidebar-hovered")
		}, function () {
			$("body").removeClass("sidebar-hovered")
		}))
	})





	$(function () {
		for (var e = window.location, o = $(".sidebar-wrapper .metismenu li a").filter(function () {
			return this.href == e
		}).addClass("").parent().addClass("mm-active"); o.is("li");) o = o.parent("").addClass("mm-show").parent("").addClass("mm-active")
	}),







		// email 

		$(".email-toggle-btn").on("click", function () {
			$(".email-wrapper").toggleClass("email-toggled")
		}), $(".email-toggle-btn-mobile").on("click", function () {
			$(".email-wrapper").removeClass("email-toggled")
		}), $(".compose-mail-btn").on("click", function () {
			$(".compose-mail-popup").show()
		}), $(".compose-mail-close").on("click", function () {
			$(".compose-mail-popup").hide()
		})


	// chat 

	$(".chat-toggle-btn").on("click", function () {
		$(".chat-wrapper").toggleClass("chat-toggled")
	}), $(".chat-toggle-btn-mobile").on("click", function () {
		$(".chat-wrapper").removeClass("chat-toggled")
	})




	// switcher 

	$("#LightTheme").on("click", function () {
		$("html").attr("data-bs-theme", "light")
	}),

		$("#DarkTheme").on("click", function () {
			$("html").attr("data-bs-theme", "dark")
		}),

		$("#SemiDarkTheme").on("click", function () {
			$("html").attr("data-bs-theme", "semi-dark")
		}),

		$("#MinimalTheme").on("click", function () {
			$("html").attr("data-bs-theme", "minimal-theme")
		})

	$("#ShadowTheme").on("click", function () {
		$("html").attr("data-bs-theme", "shadow-theme")
	})


	$(".dark-mode").click(function () {
		$("html").attr("data-bs-theme", function (i, v) {
			return v === 'dark' ? 'light1' : 'dark';
		})
	})
	$('.select2').select2({
		theme: "bootstrap-5",
		width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
		placeholder: $(this).data('placeholder'),
	});



});

// document.addEventListener('DOMContentLoaded', function () {
// 	document.querySelectorAll('.week-picker-wrapper').forEach(function (wrapper) {
// 		const inputWeek = wrapper.querySelector('.weekPicker'); // input type="week"
// 		const inputDisplay = wrapper.querySelector('.weekPicker-display'); // input hiển thị

// 		// --- Gắn Flatpickr ---
// 		const fp = flatpickr(inputDisplay, {
// 			plugins: [new weekSelect()],
// 			weekNumbers: true,
// 			dateFormat: "Y-m-d",
// 			onReady: function (_, __, instance) {
// 				// Ẩn cột số tuần
// 				const weekWrapper = instance.calendarContainer.querySelector('.flatpickr-weekwrapper');
// 				if (weekWrapper) weekWrapper.style.display = 'none';

// 				// --- Hiển thị ban đầu (khi load lại trang) ---
// 				if (inputWeek.value) {
// 					const [y, w] = inputWeek.value.split('-W');
// 					const date = getDateOfISOWeek(w, y);
// 					const weekInfo = getSchoolWeek(date);

// 					// ✅ Đặt ngày được chọn vào Flatpickr để highlight tuần
// 					instance.setDate(date, false);

// 					inputDisplay.value = `Tuần ${weekInfo.week} (${weekInfo.label})`;

// 				}
// 			},
// 			onChange: function (selectedDates) {
// 				const date = selectedDates[0];
// 				if (!date) return;

// 				const weekInfo = getSchoolWeek(date);
// 				const startOfWeek = getMonday(date);
// 				inputDisplay.value = `Tuần ${weekInfo.week} (${weekInfo.label})`;

// 				const isoYear = getISOWeekYear(startOfWeek);
// 				const isoWeek = getISOWeekNumber(startOfWeek);
// 				inputWeek.value = `${isoYear}-W${String(isoWeek).padStart(2, '0')}`;

// 				// Tự động submit form
// 				inputWeek.form?.submit();
// 			}
// 		});

// 	});

// 	// --- Các hàm hỗ trợ ---
// 	function getSchoolWeek(date) {
// 		const year = date.getFullYear();
// 		const schoolStart = new Date(year, 8, 1); // 1/9
// 		if (date < schoolStart) return getSchoolWeek(new Date(year - 1, 11, 31));
// 		const diffWeeks = Math.floor((date - schoolStart) / (7 * 24 * 60 * 60 * 1000)) + 1;
// 		return { week: diffWeeks, label: `${schoolStart.getFullYear()}-${schoolStart.getFullYear() + 1}` };
// 	}

// 	function getDateOfISOWeek(w, y) {
// 		const simple = new Date(y, 0, 1 + (w - 1) * 7);
// 		const dow = simple.getDay();
// 		const ISOweekStart = new Date(simple);
// 		if (dow <= 4)
// 			ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
// 		else
// 			ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
// 		return ISOweekStart;
// 	}

// 	function getMonday(date) {
// 		const d = new Date(date);
// 		const day = d.getDay();
// 		const diff = d.getDate() - day + (day === 0 ? -6 : 1);
// 		return new Date(d.setDate(diff));
// 	}

// 	function getISOWeekYear(date) {
// 		const tmp = new Date(date.getTime());
// 		tmp.setDate(tmp.getDate() + 4 - (tmp.getDay() || 7));
// 		return tmp.getFullYear();
// 	}

// 	function getISOWeekNumber(date) {
// 		const tmp = new Date(date.getTime());
// 		tmp.setHours(0, 0, 0, 0);
// 		tmp.setDate(tmp.getDate() + 4 - (tmp.getDay() || 7));
// 		const yearStart = new Date(tmp.getFullYear(), 0, 1);
// 		return Math.ceil((((tmp - yearStart) / 86400000) + 1) / 7);
// 	}
// });
