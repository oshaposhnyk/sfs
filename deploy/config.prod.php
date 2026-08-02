<?php

// Production Moodle config for SecureFood School.
//
// Secrets are read from files (Docker secrets) via *_FILE env vars, falling
// back to plain env vars for non-sensitive values. Never bake passwords here.

unset($CFG);
global $CFG;
$CFG = new stdClass();

/** Read a plain env var with a default. */
$env = static function (string $name, string $default): string {
    $value = getenv($name);
    return $value === false || $value === '' ? $default : $value;
};

/** Read a secret: prefer NAME_FILE (Docker secret), else NAME, else default. */
$secret = static function (string $name, string $default) use ($env): string {
    $file = getenv($name . '_FILE');
    if ($file !== false && $file !== '' && is_readable($file)) {
        return trim((string) file_get_contents($file));
    }
    return $env($name, $default);
};

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = $env('DB_HOST', 'mysql');
$CFG->dbname    = $env('DB_DATABASE', 'moodle');
$CFG->dbuser    = $env('DB_USER', 'moodle');
$CFG->dbpass    = $secret('DB_PASSWORD', '');
$CFG->prefix    = 'mdl_';
$CFG->dboptions = [
    'dbpersist'   => false,
    'dbport'      => $env('DB_PORT', '3306'),
    'dbsocket'    => false,
    'dbcollation' => 'utf8mb4_unicode_ci',
    // Uncomment when the DB enforces TLS (managed DB / separate host):
    // 'ssl_key' => null, 'ssl_cert' => null,
    // 'ssl_ca' => '/etc/ssl/certs/db-ca.pem', 'ssl_verify_server_cert' => true,
];

$CFG->wwwroot = rtrim($env('MOODLE_WWWROOT', ''), '/'); // MUST be https://your.domain
$CFG->dataroot = '/var/www/moodledata';
$CFG->admin = 'admin';

// --- TLS terminates at Caddy; Moodle sits behind the reverse proxy. ---------
$CFG->sslproxy = true;            // trust that the edge served us over HTTPS
$CFG->cookiesecure = true;        // Secure flag on session cookies
$CFG->cookiehttponly = true;
// Real client IP comes from the proxy. Only trust these headers because the
// only thing that can reach php-fpm is our own Caddy/nginx on the edge network.
$CFG->getremoteaddrconf = 0;      // honour X-Forwarded-For / X-Real-IP

// --- Redis sessions (password-protected in prod). ---------------------------
if ($env('MOODLE_REDIS_SESSIONS', '1') === '1') {
    $CFG->session_handler_class = '\\core\\session\\redis';
    $CFG->session_redis_host = $env('REDIS_HOST', 'redis');
    $CFG->session_redis_port = 6379;
    $CFG->session_redis_database = 0;
    $CFG->session_redis_prefix = 'sfs_';
    $CFG->session_redis_auth = $secret('REDIS_PASSWORD', '');
    $CFG->session_redis_acquire_lock_timeout = 120;
    $CFG->session_redis_lock_expire = 7200;
}

// --- Hardening --------------------------------------------------------------
$CFG->directorypermissions = 02770;   // not world-writable
$CFG->preventexecpath = true;          // block admin-set filesystem paths
$CFG->cronclionly = true;              // /admin/cron.php over the web is blocked
$CFG->pathtophp = '/usr/local/bin/php';
$CFG->debug = 0;
$CFG->debugdisplay = 0;
// $CFG->disableupdatenotifications kept default; keep update checks ON.

require_once(__DIR__ . '/lib/setup.php');
