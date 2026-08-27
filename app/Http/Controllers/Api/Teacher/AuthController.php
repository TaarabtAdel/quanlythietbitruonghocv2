<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\ApiToken;
use App\Models\User;
use App\Services\CampusService;
use App\Support\Api\UserPresenter;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'campus_id' => ['nullable', 'string'],
        ]);

        $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::query()
            ->whereNull('deleted_at')
            ->where($loginField, $credentials['email'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->error('Thông tin đăng nhập không chính xác.', 422, [
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        CampusService::rememberLogin($user);
        $campuses = CampusService::selectable();

        if (CampusService::hasAffiliated() && empty($credentials['campus_id'])) {
            return $this->success([
                'requires_campus_select' => true,
                'campuses' => $campuses,
                'token' => null,
                'token_type' => 'Bearer',
                'user' => UserPresenter::profile($user),
            ], 'Vui lòng chọn cơ sở.');
        }

        if (! empty($credentials['campus_id'])) {
            $error = CampusService::choose($credentials['campus_id'], $user);
            if ($error) {
                return $this->error($error, 422);
            }
            $user = Auth::user() ?: $user;
        }

        $token = ApiToken::createForUser($user);

        return $this->success([
            'requires_campus_select' => false,
            'campus_id' => TenantContext::campusKey(),
            'campuses' => $campuses,
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserPresenter::profile($user),
        ], 'Đăng nhập thành công.');
    }

    public function logout(Request $request): JsonResponse
    {
        $plain = $request->bearerToken();

        if ($plain) {
            ApiToken::query()
                ->where('token', hash('sha256', $plain))
                ->delete();
        }

        return $this->success(null, 'Đăng xuất thành công.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(UserPresenter::profile($request->user()));
    }
}
