<div class="card">
    <div class="card-body">
        <div class="mb-4">
            <label class="mb-3">Tên <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control" name="name" value="{{ $item->name ?? old('name') }}" placeholder="Nhập tên...">
            <x-form-input-error field="name"/>
        </div>
        <div class="row">
            <div class="mb-4 col-lg-6">
                <label class="mb-3">Email <span class="text-danger">(*)</span></label>
                <input type="text" class="form-control" name="email" value="{{ $item->email ?? old('email') }}" placeholder="Nhập Email...">
                <x-form-input-error field="email"/>
            </div>
            <div class="mb-4 col-lg-6">
                <label class="mb-3">Mật khẩu <span class="text-danger">(*)</span></label>
                <input type="text" class="form-control" name="password" value="" placeholder="Nhập mật khẩu...">
                <small>Nhập mật khẩu mới khi muốn cập nhật, nếu không thì giữ nguyên</small>
                <x-form-input-error field="password"/>
            </div>
        </div>
        <div class="row">
            <div class="mb-4 col-lg-6">
                <label class="mb-3">Địa chỉ</label>
                <input type="text" class="form-control" name="address" value="{{ $item->address ?? old('address') }}" placeholder="Nhập địa chỉ...">
                <x-form-input-error field="address"/>
            </div>
            <div class="mb-4 col-lg-6">
                <label class="mb-3">Số điện thoại</label>
                <input type="tel" class="form-control" name="phone" value="{{ $item->phone ?? old('phone') }}" placeholder="Nhập số điện thoại...">
                <x-form-input-error field="phone"/>
            </div>
        </div>
        <div class="row">
            <div class="mb-4 col-lg-6">
                <label class="mb-3">Giới tính</label>
                <select name="gender" class="form-control">
                    <option @selected(@$item->gender == 'Nam') value="Nam">Nam</option>
                    <option @selected(@$item->gender == 'Nữ') value="Nữ">Nữ</option>
                </select>
                <x-form-input-error field="gender"/>
            </div>
            <div class="mb-4 col-lg-6">
                <label class="mb-3">Ngày sinh</label>
                <input type="date" class="form-control" name="birthday" value="{{ $item->birthday ?? old('birthday') }}" placeholder="Nhập ngày sinh...">
                <x-form-input-error field="birthday"/>
            </div>
        </div>  
    </div>
</div>