<?php

    namespace greboid\stock;

    use \Exception;

    class ItemRoutes {

        function addRoutes($router, $smarty, $stock) {
            $router->get('/add/item', function() use ($smarty, $stock) {
                if (count($stock->getSites()) == 0) {
                    header('Location: /add/location');
                }
                try {
                    $smarty->assign('sites', $stock->getSites());
                    $smarty->assign('locations', $stock->getLocations());
                    $smarty->display('additem.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/add/item', function() use ($smarty, $stock) {
                try {
                    if (isset($_POST['name']) && isset($_POST['location']) && isset($_POST['count'])) {
                        $name = filter_var($_POST['name'], FILTER_UNSAFE_RAW);
                        $location = filter_var($_POST['location'], FILTER_UNSAFE_RAW);
                        $count = filter_var($_POST['count'], FILTER_UNSAFE_RAW);
                        $stock->insertItem($name, $location, $count);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        $smarty->display('500.tpl');
                    }
                    header('Location: /');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/edit/item/(\d+)', function($itemid) use ($smarty, $stock) {
                $count = filter_var($_POST['count'], FILTER_UNSAFE_RAW);
                $countup = filter_var($_POST['countup'], FILTER_UNSAFE_RAW);
                $countdown = filter_var($_POST['countdown'], FILTER_UNSAFE_RAW);
                try {
                    if (isset($_POST['countdown']) && isset($_POST['count'])) {
                        $stock->editItem($itemid, $count-$countdown);
                    } else if (isset($_POST['countup']) && isset($_POST['count'])) {
                        $stock->editItem($itemid, $count+$countup);
                    } else if (isset($_POST['count'])) {
                        $stock->editItem($itemid, $count);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        $smarty->display('500.tpl');
                    }
                    header('Location: /site/'.$stock->getSiteNameForItemID($itemid));
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->get('/manage/items', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('sites', $stock->getSites());
                    $smarty->assign('locations', $stock->getLocations());
                    $smarty->assign('stock', $stock->getSiteStock(0));
                    $smarty->display('manageitems.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/delete/item/(\d+)', function($itemid) use ($smarty, $stock) {
                try {
                    $stock->deleteItem($itemid);
                    header('Location: /manage/items');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
        }
    }
