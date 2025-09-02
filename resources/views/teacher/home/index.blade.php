@extends('teacher.layouts.master')
@section('title', 'Trang chủ')

@push('header')
<link rel="stylesheet" href="/admin-assets/plugins/fullcalendar/css/main.min.css">
@endpush

@section('content')
<div class="page-wrapper">
    <div class="row">
        <div class="col-12 col-lg-4">
            <div class="card radius-10 border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Tổng phiếu mượn</p>
                            <h4 class="mb-0 text-primary">0</h4>
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
                            <h4 class="mb-0 text-success">0</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-success text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4.5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%;"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
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
                            <h4 class="mb-0 text-danger">0</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-danger text-white">
                            <i class="bi bi-graph-down-arrow"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4.5px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 75%;"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
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
</div>
@endsection
<!-- Add script from @stack('styles') -->
@push('footer')
<script src="/admin-assets/plugins/fullcalendar/js/main.min.js"></script>
    <script>
    var events = [];
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: '',
                center: 'title',
                right: ''
            },
            initialView: 'dayGridMonth',
            initialDate: '2025-09-02',
            events: events
        });
        calendar.render();
    });
    </script>
@endpush