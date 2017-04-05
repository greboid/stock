<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;

    class CategoryRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $app = $storage->retrieve('app');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');

            $app->get('/categories/', function() use ($smarty, $stock) {
                return $smarty->fetch('categories.tpl');
            });
            $app->get('/category/{categoryName}', function($categoryName) use ($smarty, $stock) {
                $categoryName = filter_var($categoryName, FILTER_UNSAFE_RAW);
                $categoryID = $stock->getCategoryID($categoryName);
                $siteID = 0;
                if ($categoryID === -1) {
                    header('HTTP/1.1 404 Not Found');
                    return $smarty->fetch('404.tpl');
                }
                try {
                    $smarty->assign('siteid', $siteID);
                    $smarty->assign('site', $stock->getSiteName($siteID));
                    $smarty->assign('stock', $stock->getCategoryStock($categoryID));
                    return $smarty->fetch('stock.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->get('/add/category', function() use ($smarty, $stock) {
                try {
                    return $smarty->fetch('addCategory.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/add/category', function() use ($smarty, $stock, $app) {
                $name = filter_input(INPUT_POST, "name", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                $parent = filter_input(INPUT_POST, "parent", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                if ($parent == null) {
                    $parent = 0;
                }
                try {
                    if ($name !== false && $parent !== false) {
                        $stock->insertCategory($name, $parent);
                        return $app->redirect('/manage/categories');
                    } else if ($name !== false) {
                        $stock->insertLocation($name);
                        return $app->redirect('/manage/categories');
                    }
                    $smarty->assign('error', 'Missing required value.');
                    return $smarty->fetch('500.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/edit/category', function() use ($smarty, $stock, $app) {
                try {
                    $categoryID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT);
                    $categoryName = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $categoryParent = filter_input(INPUT_POST, "editParent", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if ($categoryParent == null) {
                        $categoryParent = 0;
                    }
                    if ($categoryName !== false) {
                        $stock->editCategory($categoryID, $categoryName, $categoryParent);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        return $smarty->fetch('500.tpl');
                    }
                    return $app->redirect('/manage/categories');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->get('/manage/categories', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('allCategoryStock', $stock->getAllCategoryStock());
                    return $smarty->fetch('managecategories.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/delete/category/{categoryID}', function($categoryid) use ($smarty, $stock, $app) {
                $categoryid = filter_var($categoryid, FILTER_VALIDATE_INT);
                try {
                    $stock->deleteCategory($categoryid);
                    return $app->redirect('/manage/categories');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
        }
    }
