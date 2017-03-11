<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Bramus\Router\Router;
    use \Smarty;

    class ItemRoutes {

        public function addRoutes(Router $router, Smarty $smarty, Stock $stock): void {
            $router->get('/add/item', function() use ($smarty, $stock) {
                if (count($stock->getSites()) == 0) {
                    header('Location: /add/location');
                }
                try {
                    $smarty->display('additem.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/add/item', function() use ($smarty, $stock) {
                $name = filter_input(INPUT_POST, "name", FILTER_UNSAFE_RAW);
                $location = filter_input(INPUT_POST, "location", FILTER_VALIDATE_INT);
                $category = filter_input(INPUT_POST, "category", FILTER_VALIDATE_INT);
                $count = filter_input(INPUT_POST, "count", FILTER_VALIDATE_INT);
                try {
                    if ($name !== false && $location !== false && $count !== false && $category !== false) {
                        $stock->insertItem($name, $location, $category, $count);
                        header('Location: /manage/items');
                    }
                    $smarty->assign('error', 'Missing required value.');
                    $smarty->display('500.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/edit/item/(\d+)', function($itemid) use ($smarty, $stock) {
                $itemid = filter_var($itemid, FILTER_VALIDATE_INT);
                $count = filter_input(INPUT_POST, $itemid."-count", FILTER_VALIDATE_INT);
                $countup = filter_input(INPUT_POST, "countup", FILTER_VALIDATE_INT);
                $countdown = filter_input(INPUT_POST, "countdown", FILTER_VALIDATE_INT);
                try {
                    if ($itemid !== null && $countdown !== null) {
                        $stock->editItem($itemid, $count-$countdown);
                        header('Location: /site/'.$stock->getSiteNameForItemID($itemid));
                    } else if ($itemid !== null && $countup !== null) {
                        $stock->editItem($itemid, $count+$countup);
                        header('Location: /site/'.$stock->getSiteNameForItemID($itemid));
                    } else if ($itemid !== null && $count !== false) {
                        $stock->editItem($itemid, $count);
                        header('Location: /site/'.$stock->getSiteNameForItemID($itemid));
                    }
                    $smarty->assign('error', 'Missing required value.');
                    $smarty->display('500.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->get('/manage/items', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('stock', $stock->getSiteStock(0));
                    $smarty->display('manageitems.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/delete/item/(\d+)', function($itemid) use ($smarty, $stock) {
                $itemid = filter_var($itemid, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
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
