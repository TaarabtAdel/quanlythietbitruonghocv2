<div class="card mt-4">
    <div class="card-body">
        <div class="product-table">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <td></td>
                            <td colspan="2" class="text-center fw-bold">Buổi sáng</td>
                            <td colspan="2" class="text-center fw-bold">Buổi chiều</td>
                        </tr>
                        <tr>
                            <td>Ngày</td>
                            <td>Tiết</td>
                            <td class="text-uppercase fw-bold">{{ $lab_name }}</td>
                            <td>Tiết</td>
                            <td class="text-uppercase fw-bold">{{ $lab_name }}</td>
                        </tr>
                        @foreach( $items as $date => $tiet_arr )
                            @php $pm = 6;  @endphp
                            @for($am = 1; $am <= 5; $am++)
                            <tr>
                                @if( $am == 1 )
                                <td rowspan="5" class="fw-bold text-danger">{{ __(date('l',strtotime($date))) }} <br> {{ __(date('d/m/Y',strtotime($date))) }}</td>
                                @endif
                                <td>{{ $am }}</td>
                                <td class="{{ !empty($tiet_arr[$am]['user_name']) ? 'bg-info' : '' }}">{{ $tiet_arr[$am]['user_name'] ?? '' }}</td>
                                <td>{{ $am }}</td>
                                <td class="{{ !empty($tiet_arr[$pm]['user_name']) ? 'bg-info' : '' }}">{{ $tiet_arr[$pm]['user_name'] ?? '' }}</td>
                            </tr>
                            @php $pm++;  @endphp
                            @endfor
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>