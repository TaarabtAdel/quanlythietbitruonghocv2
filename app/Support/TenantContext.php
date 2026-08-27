<?php

namespace App\Support;

class TenantContext
{
    protected static ?string $subdomain = null;

    protected static ?string $databaseName = null;

    protected static ?string $mainDatabase = null;

    protected static string $campusKey = 'main';

    protected static ?string $campusName = null;

    public static function set(string $subdomain, string $databaseName): void
    {
        static::$subdomain = $subdomain;
        static::$databaseName = $databaseName;
        static::$mainDatabase ??= $databaseName;
    }

    public static function setMainDatabase(string $databaseName): void
    {
        static::$mainDatabase = $databaseName;
    }

    public static function setCampus(string $key, string $name, string $databaseName): void
    {
        static::$campusKey = $key;
        static::$campusName = $name;
        static::$databaseName = $databaseName;
    }

    public static function subdomain(): ?string
    {
        return static::$subdomain;
    }

    public static function schoolSlug(): ?string
    {
        return static::$subdomain;
    }

    public static function databaseName(): ?string
    {
        return static::$databaseName;
    }

    public static function mainDatabase(): ?string
    {
        return static::$mainDatabase;
    }

    public static function campusKey(): string
    {
        return static::$campusKey;
    }

    public static function campusName(): ?string
    {
        return static::$campusName;
    }

    public static function isMainCampus(): bool
    {
        return static::$campusKey === 'main';
    }
}
