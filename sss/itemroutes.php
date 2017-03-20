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
            $router->post('/edit/item/(\d+)(.*)?', function($itemid, $route = null) use ($smarty, $stock) {
                $itemid = filter_var($itemid, FILTER_VALIDATE_INT);
                $count = filter_input(INPUT_POST, $itemid."-count", FILTER_VALIDATE_INT);
                $countup = filter_input(INPUT_POST, "countup", FILTER_VALIDATE_INT);
                $countdown = filter_input(INPUT_POST, "countdown", FILTER_VALIDATE_INT);
                try {
                    if ($itemid !== null && $countdown !== null) {
                        $stock->editItemCount($itemid, $count-$countdown);
                        header('Location: /'.$route);
                    } else if ($itemid !== null && $countup !== null) {
                        $stock->editItemCount($itemid, $count+$countup);
                        header('Location: /'.$route);
                    } else if ($itemid !== null && $count !== false) {
                        $stock->editItemCount($itemid, $count);
                        header('Location: /'.$route);
                    }
                    $smarty->assign('error', 'Missing required value.');
                    $smarty->display('500.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/edit/item', function() use ($smarty, $stock) {
                try {
                    $itemID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    $locationName = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $locationID = filter_input(INPUT_POST, "editLocation", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    $categoryID = filter_input(INPUT_POST, "editCategory", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    $stockCount = filter_input(INPUT_POST, "editCount", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    $stock->editItem($itemID, $locationName, $locationID, $categoryID, $stockCount);
                    header('Location: /manage/items');
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
