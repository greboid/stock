<?php

    namespace greboid\stock;

    use \Exception;

    class CategoryRoutes {

        function addRoutes($router, $smarty, $stock) {
            $router->get('/add/category', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('sites', $stock->getSites());
                    $smarty->assign('locations', $stock->getLocations());
                    $smarty->assign('categories', $stock->getCategories());
                    $smarty->display('addCategory.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/add/category', function() use ($smarty, $stock) {
                try {
                    if (isset($_POST['name']) && isset($_POST['parent'])) {
                        $name = filter_var($_POST['name'], FILTER_UNSAFE_RAW);
                        $parent = filter_var($_POST['parent'], FILTER_UNSAFE_RAW);
                        $stock->insertCategory($name, $parent);
                    } else if (isset($_POST['name'])) {
                        $name = filter_var($_POST['name'], FILTER_UNSAFE_RAW);
                        $stock->insertLocation($name);
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
            $router->get('/manage/categories', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('sites', $stock->getSites());
                    $smarty->assign('locations', $stock->getLocations());
                    $smarty->assign('categories', $stock->getCategories());
                    $smarty->display('managecategories.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/delete/category/(\d+)', function($categoryid) use ($smarty, $stock) {
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
