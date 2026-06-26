<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Domain & database prefix (hosting)
    |--------------------------------------------------------------------------
    | Live: {school}.quanlythietbitruonghoc.com → jzxzyfjmhosting_{school}
    | Slug trường lấy tự động từ subdomain mỗi request — không cấu hình cố định.
    */
    'base_domain' => env('TENANT_BASE_DOMAIN', 'quanlythietbitruonghoc.com'),
    'database_prefix' => env('TENANT_DATABASE_PREFIX', 'jzxzyfjmhosting_'),

    /*
    |--------------------------------------------------------------------------
    | Tenant resolution (subdomain → DB)
    |--------------------------------------------------------------------------
    | Local: TENANT_RESOLVE=false — dùng DB_DATABASE trong .env
    | Live:  TENANT_RESOLVE=true — subdomain request → DB tương ứng (nhiều trường, 1 source)
    */
    'tenant_resolve' => filter_var(env('TENANT_RESOLVE', false), FILTER_VALIDATE_BOOLEAN),

];
