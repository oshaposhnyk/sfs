<?php

unset($CFG);
global $CFG;
$CFG = new stdClass();

$env = static function(string $name, string $default): string {
    $value = getenv($name);
    return $value === false || $value === '' ? $default : $value;
};

$CFG->dbtype = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost = $env('DB_HOST', 'mysql');
$CFG->dbname = $env('DB_DATABASE', 'moodle');
$CFG->dbuser = $env('DB_USER', 'moodle');
$CFG->dbpass = $env('DB_PASSWORD', 'moodlepass');
$CFG->prefix = 'mdl_';
$CFG->dboptions = [
    'dbpersist' => false,
    'dbport' => $env('DB_PORT', '3306'),
    'dbsocket' => false,
    'dbcollation' => 'utf8mb4_unicode_ci',
];

$CFG->wwwroot = rtrim($env('MOODLE_WWWROOT', 'http://localhost:8080'), '/');
$CFG->dataroot = '/var/www/moodledata';
$CFG->admin = 'admin';
$CFG->directorypermissions = 02777;

if ($env('MOODLE_REDIS_SESSIONS', '1') === '1') {
    $CFG->session_handler_class = '\\core\\session\\redis';
    $CFG->session_redis_host = $env('REDIS_HOST', 'redis');
    $CFG->session_redis_port = 6379;
    $CFG->session_redis_database = 0;
    $CFG->session_redis_prefix = 'sfs_';
    $CFG->session_redis_acquire_lock_timeout = 120;
    $CFG->session_redis_lock_expire = 7200;
}

if ($env('MOODLE_DEBUG', '0') === '1') {
    $CFG->debug = E_ALL;
    $CFG->debugdisplay = 1;
}

require_once(__DIR__ . '/lib/setup.php');
