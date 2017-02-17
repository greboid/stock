<?php

if (file_exists(dirname( __FILE__ ).'/config.local.php')) {
    include(dirname( __FILE__ ).'/config.local.php');
}

if (!defined('LIBS_PATH')) {
    define('LIBS_PATH', '../libs/');
}
if (!defined('TEMPLATES_PATH')) {
    define('TEMPLATES_PATH', '../templates/');
}
if (!defined('TEMPLATES_CACHE_PATH')) {
    define('TEMPLATES_CACHE_PATH', '../templates_cache/');
}
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', '../cache/');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', '../configs/');
}
if (!defined('SITES_TABLE')) {
    define('SITES_TABLE', 'sites');
}
if (!defined('LOCATIONS_TABLE')) {
    define('LOCATIONS_TABLE', 'locations');
}
if (!defined('STOCK_TABLE')) {
    define('STOCK_TABLE', 'stock');
}
if (!defined('STOCK_DB')) {
    define('STOCK_DB', 'stock');
}
if (!defined('STOCK_DB_USER')) {
    define('STOCK_DB_USER', 'stock');
}
if (!defined('STOCK_DB_PW')) {
    define('STOCK_DB_PW', 'stock');
}
if (!defined('STOCK_DB_HOST')) {
    define('STOCK_DB_HOST', '127.0.0.1');
}
