<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Silex\Application;
    use \Symfony\Component\HttpFoundation\Request;

    class ItemRoutes {

        public function addRoutes(Application $app): void {

            $app->get('/add/item', function(Application $app) {
                if (count($app['stock']->getSites()) == 0) {
                    return $app->redirect('/add/location');
                }
                try {
                    return $app['twig']->render('additem.tpl', array());
                } catch (Exception $e) {
                    return $app->abprt(500, $e->getMessage());
                }
            });
            $app->post('/add/item', function(Application $app, Request $request) {
                $name = $request->get('name');
                $location = intval($request->request->get('location'), 10);
                $category = intval($request->request->get('category'), 10);
                $count = intval($request->request->get('count'), 10);
                try {
                    if ($name !== false && $location !== false && $count !== false && $category !== false) {
                        $app['stock']->insertItem($name, $location, $category, $count);
                        return $app->redirect('/manage/items');
                    } else {
                        return $app->abort(400, 'Missing Required Value');
                    }
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/edit/item/{itemid}', function(Application $app, $itemid) {
                $data = json_decode(file_get_contents('php://input'), true);
                try {
                    if ($itemid !== null) {
                        if (is_numeric($data['newcount'])) {
                            $app['stock']->editItemCount($itemid, intval($data['newcount']));
                            return intval($data['newcount']);
                        } else {
                            return $app->abort(400, 'Not an int');
                        }
                    } else {
                        return $app->abort(400, 'no item id');
                    }
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/edit/item', function(Application $app, Request $request) {
                try {
                    $itemID = intval($request->request->get('editID'), 10);
                    $locationName = $request->request->get('editName');
                    $locationID = intval($request->request->get('editLocation'), 10);
                    $categoryID = intval($request->request->get('editCategory'), 10);
                    $stockCount = intval($request->request->get('editCount'), 10);
                    $app['stock']->editItem($itemID, $locationName, $locationID, $categoryID, $stockCount);
                    return $app->redirect('/manage/items');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/manage/items', function(Application $app) {
                try {
                    return $app['twig']->render('manageitems.tpl', array(
                        'stock' => $app['stock']->getSiteStock(0),
                    ));
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/delete/item/{itemid}', function(Application $app, $itemid) {
                $itemid = filter_var($itemid, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                try {
                    $app['stock']->deleteItem($itemid);
                    return $app->redirect('/manage/items');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
        }
    }
