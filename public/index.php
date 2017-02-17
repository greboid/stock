<?php
    require './../vendor/autoload.php';
    require_once('../config.php');
    require_once('../stock.php');

    $smarty = new Smarty();
    $smarty->setTemplateDir(TEMPLATES_PATH);
    $smarty->setCompileDir(TEMPLATES_CACHE_PATH);
    $smarty->setCacheDir(CACHE_PATH);
    $smarty->setConfigDir(CONFIG_PATH);
    $error = false;

    try {
        $smarty->assign('sites', getSites());
        $smarty->assign('locations', getLocations());
    } catch (Exception $e) {
        $error = true;
        $smarty->assign('error', $e->getMessage());
        $smarty->display('500.tpl');
    }
    if (substr($_SERVER['REMOTE_ADDR'], 7) == '192.168') {
        $error = true;
        $smarty->display('403.tpl');
    }

    $site = FALSE;


    try {
        if (!$error) {
            if (isset($_REQUEST['action'])) {
                switch ($_REQUEST['action']) {
                    case 'additem':
                        if (isset($_POST['name']) && isset($_POST['location']) && isset($_POST['count'])) {
                            insertItem($_POST['name'], $_POST['location'], $_POST['count']);
                        }
                        $smarty->display('additem.tpl');
                    break;
                    case 'addlocation':
                        if (isset($_POST['name']) && isset($_POST['site'])) {
                            insertLocation($_POST['name'], $_POST['site']);
                        }
                        showTemplateWithSitesAndLocations($smarty, 'addlocation.tpl');
                    break;
                    case 'addsite':
                        if (isset($_POST['name'])) {
                            insertSite($_POST['name']);
                        }
                        showTemplateWithSitesAndLocations($smarty, 'addsite.tpl');
                    break;
                    case 'edititem':
                        if (isset($_POST['itemid']) && isset($_POST['count'])) {
                            editItem($_POST['itemid'], $_POST['count']);
                        }
                        $smarty->assign('siteid', $_REQUEST['site']);
                        $smarty->assign('site', getSiteName($_REQUEST['site']));
                        $smarty->assign('stock', getStock($_REQUEST['site']));
                        $smarty->display('stock.tpl');
                    break;
                }
            } else if (isset($_REQUEST['site']) && is_numeric($_REQUEST['site']) && getSiteName($_REQUEST['site']) !== FALSE) {
                $smarty->assign('siteid', $_REQUEST['site']);
                $smarty->assign('site', getSiteName($_REQUEST['site']));
                $smarty->assign('stock', getStock($_REQUEST['site']));
                $smarty->display('stock.tpl');
            } else {
                $smarty->display('index.tpl');
            }
        }
    } catch (Exception $e) {
        $error = true;
        $smarty->assign('error', $e->getMessage());
        $smarty->display('500.tpl');
    }

    function showTemplateWithSitesAndLocations(&$smarty, $template) {
            $smarty->assign('sites', getSites());
            $smarty->assign('locations', getLocations());
            $smarty->display($template);

    }
