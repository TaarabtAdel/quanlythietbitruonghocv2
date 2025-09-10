@extends('teacher.layouts.master')
@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Quản lí tài khoản</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Thông tin người dùng</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->
<div class="row">
    <div class="col-12 col-lg-8 col-xl-12">
        <div class="card overflow-hidden">
            <!-- <div class="profile-cover bg-light position-relative mb-4">
                <div class="user-profile-avatar shadow position-absolute top-50 start-0 translate-middle-x">
                    <img src="{{ asset($item->image) }}" alt="...">
                </div>
            </div> -->
            <div class="card-body">
                <div class="mt-2 d-flex align-items-start justify-content-between">
                    <div class="">
                        <h3 class="mb-2">{{ $item->name }}</h3>
                       
                    </div>
                    <a href="{{route('users.profileEdit') }}" class="btn btn-primary">Thay đổi thông tin</a>
                </div>
            </div>
        </div>
        @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="product-table">
                    <div class="table-responsive white-space-nowrap">
                        <table class="table align-middle">
                            <tr>
                                <th>Email </th>
                                <td>{{ $item->email }}</td>
                            </tr>
                            <tr>
                                <th>Ngày sinh</th>
                                <td>{{ $item->birthday }}</td>
                            </tr>
                            <tr>
                                <th>Giới tính</th>
                                <td>{{ $item->gender }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td>{{ $item->address }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td>{{ $item->phone }}</td>
                            </tr>
                            <tr>
                                <th>Chức vụ</th>
                                <td>{{ $item->group->name ?? ''}}</td>
                            </tr>
                            <tr>
                                <th>Tổ</th>
                                <td>{{ $item->nest->name ?? ''}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection