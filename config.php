<?php

if (file_exists(dirname( __FILE__ ).'/config.local.php')) {
    include(dirname( __FILE__ ).'/config.local.php');
}

#You probably want to define this in config.local.php
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

#You might want to change these but probably not
##Database settings
if (!defined('SITES_TABLE')) {
    define('SITES_TABLE', 'sites');
}
if (!defined('LOCATIONS_TABLE')) {
    define('LOCATIONS_TABLE', 'locations');
}
if (!defined('STOCK_TABLE')) {
    define('STOCK_TABLE', 'stock');
}
if (!defined('VERSION_TABLE')) {
    define('VERSION_TABLE', 'version');
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
if (!defined('MAX_STOCK')) {
    define('MAX_STOCK', 1000000);
}
