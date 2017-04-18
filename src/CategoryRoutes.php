<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Silex\Application;

    class CategoryRoutes {

        public function addRoutes(Application $app): void {

            $app->get('/categories', function(Application $app) {
                return $app['twig']->render('categories.tpl', array());
            });
            $app->get('/categories/{categoryName}', function(Application $app, $categoryName) {
                $categoryName = filter_var($categoryName, FILTER_UNSAFE_RAW);
                $categoryID = $app['stock']->getCategoryID($categoryName);
                $siteID = 0;
                if ($categoryID === -1) {
                    return $app->abort(404, 'Category '.$categoryID.' not found');
                }
                try {
                    return $app['twig']->render('stock.tpl', array(
                        'siteid' => $siteID,
                        'site' => $app['stock']->getSiteName($siteID),
                        'stock' => $app['stock']->getCategoryStock($categoryID),
                    ));
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/categories/add', function(Application $app) {
                try {
                    return $app['twig']->render('addCategory.tpl', array());
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/categories/add', function(Application $app) {
                $name = filter_input(INPUT_POST, "name", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                $parent = filter_input(INPUT_POST, "parent", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                if ($parent == null) {
                    $parent = 0;
                }
                try {
                    if ($name !== false && $parent !== false) {
                        $app['stock']->insertCategory($name, $parent);
                        return $app->redirect('/manage/categories');
                    } else if ($name !== false) {
                        $app['stock']->insertCategory($name);
                        return $app->redirect('/manage/categories');
                    }
                    return $app->abort(500, 'Missing required value.');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/categories/edit/{categoryID}', function(Application $app, $categoryID) {
                try {
                    $categoryID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT);
                    $categoryName = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $categoryParent = filter_input(INPUT_POST, "editParent", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if ($categoryParent == null) {
                        $categoryParent = 0;
                    }
                    if ($categoryName !== false) {
                        $app['stock']->editCategory($categoryID, $categoryName, $categoryParent);
                    } else {
                        return $app->abort(500, 'Missing required value.');
                    }
                    return $app->redirect('/manage/categories');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/categories/manage', function(Application $app) {
                try {
                    return $app['twig']->render('managecategories.tpl', array(
                        'allCategoryStock' => $app['stock']->getAllCategoryStock(),
                    ));
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/categories/delete/{categoryID}', function(Application $app, $categoryID) {
                $categoryID = filter_var($categoryID, FILTER_VALIDATE_INT);
                try {
                    $app['stock']->deleteCategory($categoryID);
                    return $app->redirect('/manage/categories');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
        }
    }
