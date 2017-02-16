<?php
    require_once('config.php');
    require_once(LIBS_PATH.'Smarty.class.php');
    require_once('stock.php');

    $smarty = new Smarty();
    $smarty->setTemplateDir(TEMPLATES_PATH);
    $smarty->setCompileDir(TEMPLATES_CACHE_PATH);
    $smarty->setCacheDir(CACHE_PATH);
    $smarty->setConfigDir(CONFIG_PATH);
    $smarty->assign('sites', getSites());

    if (substr($_SERVER['REMOTE_ADDR'], 7) == '192.168') {
        $smarty->display('403.tpl');
        die();
    }

    $smarty->assign('locations', getLocations());

    $site = FALSE;


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
                $smarty->assign('sites', getSites());
                $smarty->assign('locations', getLocations());
                $smarty->display('addlocation.tpl');
            break;
            case 'addsite':
                if (isset($_POST['name'])) {
                    insertSite($_POST['name']);
                }
                $smarty->assign('sites', getSites());
                $smarty->assign('locations', getLocations());
                $smarty->display('addsite.tpl');
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
