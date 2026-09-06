<?php

namespace App\Models;

/**
 * User dùng cho phiên đăng nhập khi admin cơ sở chính đang xem cơ sở khác.
 * Danh sách /users vẫn dùng App\Models\User trên DB cơ sở hiện tại.
 */
class AuthUser extends User
{
    protected $connection = 'school_main';

    protected $table = 'users';
}
