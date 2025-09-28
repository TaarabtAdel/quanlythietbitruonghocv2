@extends('admin.layouts.master')
@push('header')
<link rel="stylesheet" href="{{ asset('admin-assets/plugins/fullcalendar/css/main.min.css') }}">
<style>
    .fc-toolbar-title {
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-lg-4">
        <div class="card radius-10 border-0 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="">
                        <p class="mb-1">Tổng phiếu mượn</p>
                        <h4 class="mb-0 text-primary">{{ $total_borrow }}</h4>
                    </div>
                    <div class="ms-auto widget-icon bg-primary text-white">
                        <i class="bi bi-basket2-fill"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4.5px;">
                    <div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75"
                        aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card radius-10 border-0 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="">
                        <p class="mb-1">Phiếu mượn đã duyệt</p>
                        <h4 class="mb-0 text-success">{{ $total_borrow_active }}</h4>
                    </div>
                    <div class="ms-auto widget-icon bg-success text-white">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4.5px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" aria-valuenow="75"
                        aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card radius-10 border-0 border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="">
                        <p class="mb-1">Phiếu mượn chờ duyệt</p>
                        <h4 class="mb-0 text-danger">{{ $total_borrow_inactive }}</h4>
                    </div>
                    <div class="ms-auto widget-icon bg-danger text-white">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4.5px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 75%;" aria-valuenow="75"
                        aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer')
<script src="{{ asset('admin-assets/plugins/fullcalendar/js/main.min.js') }}"></script>
<script src="{{ asset('admin-assets/plugins/fullcalendar/locales/vi.js') }}"></script>
<script>
        var events = <?= json_encode($events) ?>;
		document.addEventListener('DOMContentLoaded', function () {
			var calendarEl = document.getElementById('calendar');
			var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'vi',
				headerToolbar: {
					left: '',
					center: 'title',
					right: ''
				},
				initialView: 'dayGridMonth',
				initialDate: '<?= date('Y-m-d');?>',
				events: events
			});
			calendar.render();
		});
	</script>
@endpush