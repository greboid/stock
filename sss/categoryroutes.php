<?php

    namespace greboid\stock;

    use \Exception;

    class CategoryRoutes {

        public function addRoutes($router, $smarty, $stock) {
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
                $name = filter_input(INPUT_POST, "name", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                $parent = filter_input(INPUT_POST, "parent", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                try {
                    if ($name !== false && $parent !== false) {
                        $stock->insertCategory($name, $parent);
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
