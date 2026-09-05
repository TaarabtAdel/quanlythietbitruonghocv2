<?php

namespace App\Services;

use App\Models\Campus;
use App\Models\User;
use App\Support\TenantContext;
use App\Support\TenantDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampusService
{
    public const MAIN_KEY = 'main';

    public const SESSION_KEY = 'campus_key';

    public const SESSION_LOGIN_EMAIL = 'campus_login_email';

    public const SESSION_LOGIN_PHONE = 'campus_login_phone';

    public const SESSION_MAIN_ADMIN = 'campus_is_main_admin';

    public static function ensureReady(): void
    {
        Campus::ensureSchema();
    }

    public static function affiliated(): Collection
    {
        self::ensureReady();

        return Campus::query()
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public static function hasAffiliated(): bool
    {
        return self::affiliated()->isNotEmpty();
    }

    public static function mainName(): string
    {
        try {
            $name = DB::connection('school_main')
                ->table('options')
                ->where('option_group', 'general')
                ->where('option_name', 'company_name')
                ->value('option_value');
        } catch (\Throwable) {
            $name = null;
        }

        $name = trim((string) $name);

        return $name !== '' ? $name : 'Cơ sở chính';
    }

    /**
     * @return list<array{key: string, name: string, database: string, is_main: bool}>
     */
    public static function selectable(): array
    {
        $mainDb = TenantContext::mainDatabase() ?: (string) config('database.connections.mysql.database');

        $items = [[
            'key' => self::MAIN_KEY,
            'name' => self::mainName(),
            'database' => $mainDb,
            'is_main' => true,
        ]];

        foreach (self::affiliated() as $campus) {
            $items[] = [
                'key' => (string) $campus->id,
                'name' => $campus->name,
                'database' => $campus->database_name,
                'is_main' => false,
            ];
        }

        return $items;
    }

    public static function rememberLogin(?User $user): void
    {
        if (! $user || ! request()->hasSession()) {
            return;
        }

        session([
            self::SESSION_LOGIN_EMAIL => $user->email,
            self::SESSION_LOGIN_PHONE => $user->phone,
        ]);
    }

    public static function forget(): void
    {
        if (! request()->hasSession()) {
            return;
        }

        session()->forget([
            self::SESSION_KEY,
            self::SESSION_LOGIN_EMAIL,
            self::SESSION_LOGIN_PHONE,
            self::SESSION_MAIN_ADMIN,
        ]);
    }

    public static function applySelected(?string $keyOverride = null): void
    {
        $mainDb = TenantContext::mainDatabase() ?: (string) config('database.connections.mysql.database');
        $mainName = self::mainName();

        if (! self::hasAffiliated()) {
            TenantDatabase::connect($mainDb);
            TenantContext::setCampus(self::MAIN_KEY, $mainName, $mainDb);
            self::putSessionKey(self::MAIN_KEY);

            return;
        }

        $key = $keyOverride ?: self::sessionKey();
        $target = self::resolve($key);

        if (! $target) {
            TenantDatabase::connect($mainDb);
            TenantContext::setCampus(self::MAIN_KEY, $mainName, $mainDb);

            return;
        }

        TenantDatabase::connect($target['database']);
        TenantContext::setCampus($target['key'], $target['name'], $target['database']);

        self::bindMainAdminAuth();
    }

    /**
     * @return array{key: string, name: string, database: string, is_main: bool}|null
     */
    public static function resolve(string $key): ?array
    {
        foreach (self::selectable() as $campus) {
            if ($campus['key'] === $key) {
                return $campus;
            }
        }

        return null;
    }

    public static function connectTo(string $key): ?string
    {
        $target = self::resolve($key);

        if (! $target) {
            return 'Cơ sở không hợp lệ.';
        }

        if (! TenantDatabase::exists($target['database'])) {
            return 'Không kết nối được database của cơ sở: '.$target['database'];
        }

        TenantDatabase::connect($target['database']);
        TenantContext::setCampus($target['key'], $target['name'], $target['database']);
        self::bindMainAdminAuth();

        return null;
    }

    public static function choose(string $key, ?User $fromUser = null): ?string
    {
        $target = self::resolve($key);

        if (! $target) {
            return 'Cơ sở không hợp lệ.';
        }

        if (! TenantDatabase::exists($target['database'])) {
            return 'Không kết nối được database của cơ sở: '.$target['database'];
        }

        $email = $fromUser?->email
            ?: (request()->hasSession() ? session(self::SESSION_LOGIN_EMAIL) : null)
            ?: Auth::user()?->email;
        $phone = $fromUser?->phone
            ?: (request()->hasSession() ? session(self::SESSION_LOGIN_PHONE) : null)
            ?: Auth::user()?->phone;

        TenantDatabase::connect($target['database']);
        TenantContext::setCampus($target['key'], $target['name'], $target['database']);

        $user = self::findUser($email, $phone);

        if (! $user) {
            $mainDb = TenantContext::mainDatabase();
            if ($mainDb) {
                TenantDatabase::connect($mainDb);
            }

            return 'Tài khoản không tồn tại tại cơ sở này. Liên hệ quản trị viên để được cấp tài khoản.';
        }

        if (request()->hasSession()) {
            Auth::login($user);
            session([self::SESSION_KEY => $target['key']]);
        } else {
            Auth::setUser($user);
        }
        self::rememberLogin($user);

        return null;
    }

    public static function findUser(?string $email, ?string $phone): ?User
    {
        $email = $email ? trim($email) : null;
        $phone = $phone ? trim($phone) : null;

        if (! $email && ! $phone) {
            return null;
        }

        return User::query()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($email, $phone) {
                if ($email) {
                    $query->where('email', $email);
                }
                if ($phone) {
                    $query->orWhere('phone', $phone);
                }
            })
            ->first();
    }

    public static function needsSelection(): bool
    {
        if (! self::hasAffiliated()) {
            return false;
        }

        $key = self::sessionKey();

        return self::resolve($key) === null;
    }

    protected static function sessionKey(): string
    {
        if (! request()->hasSession()) {
            return '';
        }

        return (string) session(self::SESSION_KEY, '');
    }

    public static function markMainAdmin(bool $isMain): void
    {
        if (! request()->hasSession()) {
            return;
        }

        session([self::SESSION_MAIN_ADMIN => $isMain]);
    }

    public static function isMainAdmin(): bool
    {
        if (! request()->hasSession()) {
            return TenantContext::isMainCampus();
        }

        return (bool) session(self::SESSION_MAIN_ADMIN, TenantContext::isMainCampus());
    }

    public static function canBrowseCampuses(): bool
    {
        return self::isMainAdmin() && self::hasAffiliated();
    }

    public static function canManageCampuses(): bool
    {
        return self::isMainAdmin();
    }

    public static function bindMainAdminAuth(): void
    {
        if (self::isMainAdmin() && ! TenantContext::isMainCampus()) {
            app()->instance('campus.auth_on_main', true);
        }
    }

    public static function databasePrefix(): string
    {
        return (string) config('tenant.database_prefix');
    }

    public static function qualifyDatabaseName(string $name): string
    {
        $name = trim($name);
        $prefix = self::databasePrefix();

        if ($name === '' || $prefix === '') {
            return $name;
        }

        if (str_starts_with($name, $prefix)) {
            return $name;
        }

        return $prefix.$name;
    }

    public static function unqualifyDatabaseName(string $name): string
    {
        $prefix = self::databasePrefix();

        if ($prefix !== '' && str_starts_with($name, $prefix)) {
            return substr($name, strlen($prefix));
        }

        return $name;
    }

    public static function suggestedDatabaseName(string $campusName): string
    {
        $slug = \Illuminate\Support\Str::slug($campusName, '_');
        if ($slug === '') {
            $slug = 'coso';
        }

        $school = TenantContext::schoolSlug() ?: 'cs';

        return self::qualifyDatabaseName($school.'_'.$slug);
    }

    public static function provisionDatabase(string $databaseName, ?string $campusName = null): void
    {
        @set_time_limit(180);

        $safe = preg_replace('/[^A-Za-z0-9_-]/', '', $databaseName);
        $main = TenantContext::mainDatabase();

        if ($safe === '' || ! $main) {
            throw new \RuntimeException('Không xác định được tên database.');
        }

        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$safe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Throwable) {
            // cPanel thường không cho CREATE DATABASE bằng SQL — DB phải tạo sẵn.
        }

        if (! TenantDatabase::exists($safe)) {
            throw new \RuntimeException(
                'Không kết nối được database `'.$safe.'`. '
                .'Trên cPanel: MySQL Databases → tạo đúng tên này → Add User To Database (ALL PRIVILEGES) với user của website, rồi thử lại.'
            );
        }

        $source = DB::connection('school_main');
        $target = TenantDatabase::using('campus_provision', $safe);

        try {
            $target->getPdo();
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'User MySQL chưa được gán vào database `'.$safe.'`. '.$e->getMessage()
            );
        }

        try {
            $target->statement('SET FOREIGN_KEY_CHECKS=0');
            $cloned = self::cloneTableStructures($source, $target);

            if ($cloned === 0) {
                throw new \RuntimeException(
                    'Không clone được bảng nào sang `'.$safe.'`. Kiểm tra quyền CREATE của user MySQL trên database này.'
                );
            }

            foreach (['options', 'groups', 'roles', 'groups_roles', 'borrow_purposes'] as $table) {
                self::copyTableRows($source, $target, $table);
            }

            self::copyAdminUsers($source, $target);
            self::copyNestsForCopiedUsers($source, $target);

            if ($campusName) {
                $target->table('options')
                    ->where('option_group', 'general')
                    ->where('option_name', 'company_name')
                    ->update(['option_value' => $campusName]);
            }

            $target->statement('SET FOREIGN_KEY_CHECKS=1');
        } finally {
            DB::purge('campus_provision');
        }
    }

    protected static function cloneTableStructures($source, $target): int
    {
        try {
            $rows = $source->select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        } catch (\Throwable) {
            try {
                $rows = $source->select('SHOW TABLES');
            } catch (\Throwable $e) {
                throw new \RuntimeException('Không đọc được danh sách bảng cơ sở chính. '.$e->getMessage());
            }
        }

        $cloned = 0;

        foreach ($rows as $row) {
            $table = self::tableNameFromShowRow($row);
            if (! $table || $table === 'campuses') {
                continue;
            }

            try {
                $create = $source->select('SHOW CREATE TABLE `'.$table.'`');
                $sql = ((array) $create[0])['Create Table'] ?? null;
                if (! is_string($sql) || $sql === '') {
                    throw new \RuntimeException('Không lấy được cấu trúc bảng.');
                }

                $sql = preg_replace('/^CREATE TABLE/i', 'CREATE TABLE IF NOT EXISTS', $sql, 1);
                $sql = preg_replace('/AUTO_INCREMENT=\d+\s*/i', '', $sql);
                $target->unprepared($sql);
                $cloned++;
            } catch (\Throwable $e) {
                throw new \RuntimeException('Không clone được bảng `'.$table.'`: '.$e->getMessage());
            }
        }

        return $cloned;
    }

    protected static function tableNameFromShowRow(object $row): ?string
    {
        foreach ((array) $row as $key => $value) {
            if (strcasecmp((string) $key, 'Table_type') === 0) {
                continue;
            }

            return is_string($value) && $value !== '' ? $value : null;
        }

        return null;
    }

    protected static function copyTableRows($source, $target, string $table): void
    {
        try {
            if ($target->table($table)->count() > 0) {
                return;
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('Không đọc được bảng `'.$table.'` trên cơ sở mới: '.$e->getMessage());
        }

        try {
            $rows = $source->table($table)->get();
        } catch (\Throwable $e) {
            throw new \RuntimeException('Không đọc được bảng `'.$table.'` từ cơ sở chính: '.$e->getMessage());
        }

        if ($rows->isEmpty()) {
            return;
        }

        try {
            foreach ($rows->chunk(50) as $chunk) {
                $target->table($table)->insert($chunk->map(fn ($row) => (array) $row)->all());
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('Không copy được dữ liệu bảng `'.$table.'`: '.$e->getMessage());
        }
    }

    protected static function copyAdminUsers($source, $target): void
    {
        if ($target->table('users')->count() > 0) {
            return;
        }

        $admins = $source->table('users')
            ->where('group_id', 1)
            ->whereNull('deleted_at')
            ->get();

        $authId = auth()->id();
        if ($authId && $admins->where('id', $authId)->isEmpty()) {
            $current = $source->table('users')->where('id', $authId)->first();
            if ($current) {
                $admins->push($current);
            }
        }

        if ($admins->isEmpty()) {
            throw new \RuntimeException('Không tìm thấy tài khoản admin (group_id = 1) để copy sang cơ sở mới.');
        }

        try {
            $target->table('users')->insert($admins->map(fn ($row) => (array) $row)->all());
        } catch (\Throwable $e) {
            throw new \RuntimeException('Không copy được tài khoản admin: '.$e->getMessage());
        }
    }

    protected static function copyNestsForCopiedUsers($source, $target): void
    {
        $nestIds = $target->table('users')
            ->whereNotNull('nest_id')
            ->where('nest_id', '>', 0)
            ->distinct()
            ->pluck('nest_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($nestIds->isEmpty()) {
            return;
        }

        $existing = $target->table('nests')->whereIn('id', $nestIds)->pluck('id')->map(fn ($id) => (int) $id);
        $missing = $nestIds->diff($existing)->values();

        if ($missing->isEmpty()) {
            return;
        }

        $nests = $source->table('nests')->whereIn('id', $missing)->get();
        if ($nests->isEmpty()) {
            return;
        }

        try {
            $target->table('nests')->insert($nests->map(fn ($row) => (array) $row)->all());
        } catch (\Throwable $e) {
            throw new \RuntimeException('Không copy được tổ (nests): '.$e->getMessage());
        }
    }

    public static function putSessionKey(string $key): void
    {
        if (! request()->hasSession()) {
            return;
        }

        session([self::SESSION_KEY => $key]);
    }
}
