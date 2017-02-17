<?php
    require './../vendor/autoload.php';
    require_once('../config.php');

    use greboid\stock\Stock;

    $stock = new Stock();
    $smarty = new Smarty();
    $smarty->setTemplateDir(TEMPLATES_PATH);
    $smarty->setCompileDir(TEMPLATES_CACHE_PATH);
    $smarty->setCacheDir(CACHE_PATH);
    $smarty->setConfigDir(CONFIG_PATH);
    $error = false;

    try {
        $smarty->assign('sites', $stock->getSites());
        $smarty->assign('locations', $stock->getLocations());
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
                            $stock->insertItem($_POST['name'], $_POST['location'], $_POST['count']);
                        }
                        $smarty->display('additem.tpl');
                    break;
                    case 'addlocation':
                        if (isset($_POST['name']) && isset($_POST['site'])) {
                            $stock->insertLocation($_POST['name'], $_POST['site']);
                        }
                        showTemplateWithSitesAndLocations($smarty, $stock, 'addlocation.tpl');
                    break;
                    case 'addsite':
                        if (isset($_POST['name'])) {
                            $stock->insertSite($_POST['name']);
                        }
                        showTemplateWithSitesAndLocations($smarty, $stock, 'addsite.tpl');
                    break;
                    case 'edititem':
                        if (isset($_POST['itemid']) && isset($_POST['count'])) {
                            $stock->editItem($_POST['itemid'], $_POST['count']);
                        }
                        $smarty->assign('siteid', $_REQUEST['site']);
                        $smarty->assign('site', $stock->getSiteName($_REQUEST['site']));
                        $smarty->assign('stock', $stock->getStock($_REQUEST['site']));
                        $smarty->display('stock.tpl');
                    break;
                }
            } else if (isset($_REQUEST['site'])
                       && is_numeric($_REQUEST['site'])
                       && $stock->getSiteName($_REQUEST['site']) !== FALSE) {
                $smarty->assign('siteid', $_REQUEST['site']);
                $smarty->assign('site', $stock->getSiteName($_REQUEST['site']));
                $smarty->assign('stock', $stock->getStock($_REQUEST['site']));
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

    function showTemplateWithSitesAndLocations($smarty, $stock, $template) {
            $smarty->assign('sites', $stock->getSites());
            $smarty->assign('locations', $stock->getLocations());
            $smarty->display($template);

    }
