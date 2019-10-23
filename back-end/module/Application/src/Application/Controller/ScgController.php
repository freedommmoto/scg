<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
//use Zend\Db\Adapter\Adapter;
use Application\Models\Users;
use Zend\View\Model\JsonModel;
use Zend\Cache\StorageFactory;
use Application\Services\GooglePlaceApi;
use Application\Services\MathematicsService;

class ScgController extends AbstractActionController
{

    public function __construct()
    {
        $this->cacheTime = 36000;
//        $this->now = date('Y-m-d H:i:s');
//        $this->config = include __DIR__ . '../../../../config/module.config.php';
//        $this->adapter = new Adapter($this->config['Db']);
    }

    public function _initCache()
    {
        $cache = StorageFactory::factory([
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'ttl' => $this->cacheTime,
                ],
            ],
            'plugins' => [
                // Don't throw exceptions on cache errors
                'exception_handler' => [
                    'throw_exceptions' => true
                ],
                'Serializer',
            ],
        ]);
        return $cache;
    }

    public function indexAction()
    {
        try {
            $data = ['ScgController' => true];
            return new JsonModel($data);

        } catch (Exception $e) {
            print_r($e);
        }
    }

    public function restaurantsAction()
    {
        $cache = $this->_initCache();
        $cacheKey = 'restaurants';
        $data = $cache->getItem($cacheKey);
        //$cache->removeItems([$cacheKey]);

        if (empty($data)) {
            $googleService = new GooglePlaceApi();
            $data = $googleService->getAllPage();
            $cache->setItem($cacheKey, $data);
        }

        return new JsonModel(
            ['restaurants' => $data]
        );
    }

    public function Action()
    {
        $cache = $this->_initCache();
        $cacheKey = 'restaurants';
        $data = $cache->getItem($cacheKey);

        if (empty($data)) {
            $googleService = new GooglePlaceApi();
            $data = $googleService->getAllPage(true);
            $cache->setItem($cacheKey, $data);
        }

        return new JsonModel(
            ['restaurants' => $data]
        );
    }

    public function findXYZAction()
    {
        try {
            $cache = $this->_initCache();
            $cacheKey = 'findXYZ';
            //$cache->removeItems([$cacheKey]);

            $data = $cache->getItem($cacheKey);

            if (empty($data)) {
                $math = new MathematicsService();
                $data = $math->findXYZValue();
                $cache->setItem($cacheKey, $data);
            }

            return new JsonModel(
                ['XYZ' => $data]
            );

        } catch (Exception $e) {
            print_r($e);
        }
    }

    public function testAction()
    {
        try {
            $models = new Users($this->adapter, 1, 1);
            $user = $models->getList();
            $googleService = new GooglePlaceApi();
            $name = $googleService->getAllPage(true);
            return new JsonModel([
                $user, $name,
                'status' => getenv('DB_USERNAME'),
            ]);

        } catch (Exception $e) {
            print_r($e);
        }
    }

}