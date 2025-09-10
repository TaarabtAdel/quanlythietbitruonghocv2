@extends('teacher.layouts.master')

@section('content')
    @include('globals.breadcrumb')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.postProfileEdit') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">Tên</h6>
                                <input type="text" class="form-control" value="{{ $item->name }}" name="name">
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">Ngày sinh</h6>
                                <input type="date" class="form-control" value="{{ $item->birthday }}" name="birthday">
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">Giới tính</h6>
                                <select name="gender" class="form-control">
                                    <option value="Nam" {{ $item->gender == "Nam" ? "selected" : '' }}>Nam</option>
                                    <option value="Nữ" {{ $item->gender == "Nữ" ? "selected" : '' }}>Nữ</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">Địa chỉ</h6>
                                <input type="text" class="form-control" value="{{ $item->address }}" name="address">
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">Số điện thoại</h6>
                                <input type="number" class="form-control" value="{{ $item->phone }}" name="phone">
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">Mật khẩu</h6>
                                <input type="password" class="form-control" name="password">
                                <small>Nhập mật khẩu mới để thay đổi, nếu không thì để trống</small>
                            </div>

                            <!-- Additional form fields or sections go here -->

                        </div>

                        <div class="mb-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection