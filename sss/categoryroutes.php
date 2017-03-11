<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Bramus\Router\Router;
    use \Smarty;

    class CategoryRoutes {

        public function addRoutes(Router $router, Smarty $smarty, Stock $stock): void {
            $router->get('/categories/', function() use ($smarty, $stock) {
                $smarty->display('categories.tpl');
            });
            $router->get('/category/(.*)', function($categoryName) use ($smarty, $stock) {
                $categoryName = filter_var($categoryName, FILTER_UNSAFE_RAW);
                $categoryID = $stock->getCategoryID($categoryName);
                $siteID = 0;
                if ($categoryID === -1) {
                    header('HTTP/1.1 404 Not Found');
                    $smarty->display('404.tpl');
                }
                try {
                    $smarty->assign('siteid', $siteID);
                    $smarty->assign('site', $stock->getSiteName($siteID));
                    $smarty->assign('stock', $stock->getCategoryStock($categoryID));
                    $smarty->display('stock.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->get('/add/category', function() use ($smarty, $stock) {
                try {
                    $smarty->display('addCategory.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/add/category', function() use ($smarty, $stock) {
                $name = filter_input(INPUT_POST, "name", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                $parent = filter_input(INPUT_POST, "parent", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                if ($parent == null) {
                    $parent = 0;
                }
                try {
                    if ($name !== false && $parent !== false) {
                        $stock->insertCategory($name, $parent);
                        header('Location: /');
                    } else if ($name !== false) {
                        $stock->insertLocation($name);
                        header('Location: /');
                    }
                    $smarty->assign('error', 'Missing required value.');
                    $smarty->display('500.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->get('/manage/categories', function() use ($smarty, $stock) {
                try {
                    $smarty->display('managecategories.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/delete/category/(\d+)', function($categoryid) use ($smarty, $stock) {
                $categoryid = filter_var($categoryid, FILTER_VALIDATE_INT);
                try {
                    $stock->deleteCategory($categoryid);
                    header('Location: /manage/categories');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
        }
    }
