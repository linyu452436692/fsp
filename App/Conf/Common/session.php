<?php
return [
    'handler' => \FES\Core\CacheSession::class,
    'sess_expiration' => 7200,
    'sess_save_path' => NULL,
    'sess_cookie_name' => 'fes_session',
    'cookie_prefix' => '',
    'cookie_lifetime' => 0,
    'cookie_domain' => '',
    'cookie_path' => '',
    'cookie_secure' => FALSE,
    'cookie_httponly' => FALSE,
];