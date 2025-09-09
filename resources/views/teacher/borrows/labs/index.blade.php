@extends('admintheme::layouts.master')
@section('content')
    @include('admintheme::includes.globals.breadcrumb',[
        'page_title' => 'Lịch Báo Sử Dụng Phòng',
        'actions' => [
            
        ]
    ])
    <p class="mb-2">Lưu ý: Xem chi tiết một phòng bằng cách chọn Phòng Học</p>
    <!-- Item actions -->
    <form action="{{ route($route_prefix.'labs') }}" method="get">
        <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
                <label class="form-label fw-bold">Giáo Viên</label>
                <x-admintheme::form-input-users name="user_id" selected_id="{{ request()->user_id }}" autoSubmit="1"/>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-12">
                <label class="form-label fw-bold">Buổi</label>
                <select name="session" class="form-control" onchange="this.form.submit()">
                    <option value="">---</option>
                    <option @selected(request()->session == 'AM') value="AM">Sáng</option>
                    <option @selected(request()->session == 'PM') value="PM">Chiều</option>
                </select>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <label class="form-label fw-bold">Phòng Học</label>
                <x-admintheme::form-input-labs name="lab_id" selected_id="{{ request()->lab_id }}" autoSubmit="1" />
            </div>
            <div class="col-lg-2 col-md-6 col-sm-12">
                <label class="form-label fw-bold">Môn Học</label>
                <x-admintheme::form-input-departments name="department_id" selected_id="{{ request()->department_id }}" autoSubmit="1"/>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-12">
                <label class="form-label fw-bold">Ngày dạy : Tuần</label>
                <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control" value="{{ request()->week }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    <div class="card mt-4">
        <div class="card-body">
            <div class="product-table">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            
                        </thead>
                        <tbody>
                            @foreach( $items as $date => $tiet_arr )
                                <tr> <td colspan="3" class="fw-bold bg-info">{{ __(date('l',strtotime($date))) }} - {{ date('d/m/Y',strtotime($date)) }}</td> </tr>
                                <tr>
                                    <th>Tiết</th>
                                    <th>Buổi</th>
                                    <th>Phòng<span class="float-end">Giáo Viên</span></th>
                                </tr>
                                @if( request()->session == 'AM' || request()->session == '' )
                                    <!-- SÁNG -->
                                    <tr>
                                        <td colspan="3" class="fw-bold">BUỔI SÁNG</td>
                                    </tr>
                                    @foreach( $tiet_arr as $tiet => $labs )
                                        @php if($tiet > 5) continue @endphp
                                        <tr>
                                            <td>{{ $tiet > 5 ? $tiet - 5 : $tiet }}</td>
                                            <td>{{ $tiet > 5 ? 'Chiều' : 'Sáng' }}</td>
                                            <td>
                                                <ul class="list-group list-group-flush">
                                                    @foreach($labs['labs'] as $lab)
                                                    <li class="list-group-item">
                                                        @if( !empty($lab['lab_name']) )
                                                        {{ $lab['lab_name'] }}
                                                        <span class="float-end">{{ $lab['user_name'] }}</span>
                                                        @endif
                                                    </li>    
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                @if( request()->session == 'PM' || request()->session == '' )
                                    <!-- CHIỀU -->
                                    <tr>
                                        <td colspan="3" class="fw-bold">BUỔI CHIỀU</td>
                                    </tr>
                                    @foreach( $tiet_arr as $tiet => $labs )
                                        @php if($tiet < 6) continue @endphp
                                        <tr>
                                            <td>{{ $tiet > 5 ? $tiet - 5 : $tiet }}</td>
                                            <td>{{ $tiet > 5 ? 'Chiều' : 'Sáng' }}</td>
                                            <td>
                                                <ul class="list-group list-group-flush">
                                                    @foreach($labs['labs'] as $lab)
                                                    <li class="list-group-item">
                                                        @if( !empty($lab['lab_name']) )
                                                        {{ $lab['lab_name'] }}
                                                        <span class="float-end">{{ $lab['user_name'] }}</span>
                                                        @endif
                                                    </li>    
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection