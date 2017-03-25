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
                    http_response_code(500);
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
                    http_response_code(400);
                    $smarty->assign('error', 'Missing required value.');
                    $smarty->display('500.tpl');
                } catch (Exception $e) {
                    http_response_code(500);
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/edit/item/(\d+)', function($itemid) use ($smarty, $stock) {
                $itemid = filter_var($itemid, FILTER_VALIDATE_INT);
                $data = json_decode(file_get_contents('php://input'), true);
                try {
                    if ($itemid !== null) {
                        if (is_numeric($data['newcount'])) {
                            $stock->editItemCount($itemid, intval($data['newcount']));
                            http_response_code(200);
                            echo intval($data['newcount']);
                        } else {
                            http_response_code(400);
                            echo 'not an int';
                        }
                    } else {
                        http_response_code(400);
                        echo 'no item id';
                    }
                } catch (Exception $e) {
                    http_response_code(500);
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
                    http_response_code(500);
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
                    http_response_code(500);
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
        }
    }
