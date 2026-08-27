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
        $safe = preg_replace('/[^A-Za-z0-9_]/', '', $databaseName);
        $main = TenantContext::mainDatabase();

        if ($safe === '' || ! $main) {
            throw new \RuntimeException('Không xác định được tên database.');
        }

        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$safe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Throwable) {
            // Shared hosting / cPanel thường không cho CREATE DATABASE bằng SQL.
        }

        if (! TenantDatabase::exists($safe)) {
            throw new \RuntimeException(
                'Không tạo/kết nối được database `'.$safe.'`. '
                .'Trên cPanel vào MySQL Databases: tạo database đúng tên này, gán user MySQL của website (ALL PRIVILEGES) vào database đó, rồi thêm lại cơ sở.'
            );
        }

        $tables = DB::select('SHOW TABLES FROM `'.$main.'`');
        $column = 'Tables_in_'.$main;

        foreach ($tables as $row) {
            $table = $row->{$column} ?? null;
            if (! $table || $table === 'campuses') {
                continue;
            }

            DB::statement("CREATE TABLE IF NOT EXISTS `{$safe}`.`{$table}` LIKE `{$main}`.`{$table}`");
        }

        foreach (['options', 'groups', 'roles', 'groups_roles', 'nests', 'borrow_purposes'] as $table) {
            try {
                $count = DB::select("SELECT COUNT(*) AS c FROM `{$safe}`.`{$table}`");
                if ((int) ($count[0]->c ?? 0) > 0) {
                    continue;
                }
                DB::statement("INSERT INTO `{$safe}`.`{$table}` SELECT * FROM `{$main}`.`{$table}`");
            } catch (\Throwable) {
            }
        }

        self::copyAdminUsers($safe, $main);

        if ($campusName) {
            try {
                DB::update(
                    "UPDATE `{$safe}`.`options` SET option_value = ? WHERE option_group = 'general' AND option_name = 'company_name'",
                    [$campusName]
                );
            } catch (\Throwable) {
            }
        }
    }

    protected static function copyAdminUsers(string $safe, string $main): void
    {
        try {
            $count = DB::select("SELECT COUNT(*) AS c FROM `{$safe}`.`users`");
            if ((int) ($count[0]->c ?? 0) > 0) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        try {
            DB::statement("
                INSERT INTO `{$safe}`.`users`
                SELECT * FROM `{$main}`.`users`
                WHERE `group_id` = 1 AND `deleted_at` IS NULL
            ");
        } catch (\Throwable) {
        }

        $authId = auth()->id();
        if (! $authId) {
            return;
        }

        try {
            $exists = DB::select("SELECT COUNT(*) AS c FROM `{$safe}`.`users` WHERE `id` = ?", [$authId]);
            if ((int) ($exists[0]->c ?? 0) > 0) {
                return;
            }

            DB::statement("
                INSERT INTO `{$safe}`.`users`
                SELECT * FROM `{$main}`.`users`
                WHERE `id` = ?
            ", [$authId]);
        } catch (\Throwable) {
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
