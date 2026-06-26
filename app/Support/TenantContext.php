<?php

namespace App\Support;

class TenantContext
{
    protected static ?string $subdomain = null;

    protected static ?string $databaseName = null;

    public static function set(string $subdomain, string $databaseName): void
    {
        static::$subdomain = $subdomain;
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
}
