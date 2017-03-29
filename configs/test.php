<?php

if (file_exists(dirname( __FILE__ ).'/test.local.php')) {
    include(dirname( __FILE__ ).'/test.local.php');
}

//This is required for testing - change this to a database you do not care about
//It will be wiped when the tests are done.
if (!defined('STOCK_DB')) {
    define('STOCK_DB', 'stocktest');
}
if (!defined('STOCK_DB_USER')) {
    define('STOCK_DB_USER', 'stocktest');
}
if (!defined('STOCK_DB_PW')) {
    define('STOCK_DB_PW', 'stocktest');
}
if (!defined('STOCK_DB_HOST')) {
    define('STOCK_DB_HOST', '127.0.0.1');
}
if (!defined('ACCOUNTS_TABLE')) {
    define('ACCOUNTS_TABLE', 'accounts');
}
if (!defined('CATEGORIES_TABLE')) {
    define('CATEGORIES_TABLE', 'categories');
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
if (!defined('VERSION_TABLE')) {
    define('VERSION_TABLE', 'version');
}
