<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Application\Models\Users;
use Application\Models\LineLog;
use Application\Models\LineUser;
use Application\Models\RestaurantOrder;
use Application\Models\Restaurants;

use Zend\View\Model\JsonModel;
use Zend\Cache\StorageFactory;
use Application\Services\GooglePlaceApi;
use Application\Services\LineBotApi;
use Application\Services\MathematicsService;

class ScgController extends AbstractActionController
{

    public function __construct()
    {
        $this->cacheTime = 36000;
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

    public function linewebhockAction()
    {
        try {
            $lineLog = new LineLog();
            $lineApi = new LineBotApi();
            $restaurant = new Restaurants();
            //file_put_contents('asd.txt', print_r('-1',true) . PHP_EOL, FILE_APPEND);
            //$lineApi->testWebHook();return ;

            $inputArray = $lineApi->getInput();
            $logID = $lineLog->insert($inputArray);
            $lineUser = new LineUser();
            $lineUser->setLineUserID($lineApi->getUserID());
            $userData = $lineUser->getUserData();

            if (empty($userData)) {
                $lineUser->saveNewUser();
                $userData['restaurant_id'] = 0;
            }

            if ($lineApi->checkUserIsOrder()) { // 1)
                $restaurants = $restaurant->getList();
                $lineApi->sendRestaurantsList($restaurants);

            } else if ($lineApi->checkUserIsSelectRestaurant()) { // 2)

                $restaurantID = $lineApi->getRestaurantID();
                $restaurant = $restaurant->getDetails($restaurantID);

                $lineApi->sendRestaurantDetails($restaurant); // 3)
                $lineUser->updateUserRestaurant($restaurantID);

            } else if ($userData['restaurant_id'] > 0) { // 4)
                $lineApi->sendThankyouMessage();

                $rOrder = new RestaurantOrder();
                $rOrder->insert($userData, $inputArray);
                $lineUser->updateUserRestaurant(0);

            } else { // 5)
                $lineUser->updateUserRestaurant(0);
                $lineApi->sendNotUndestandMessage();
            }

            $lineLog->update($logID, $lineApi->getOutput(), $lineApi->getOutputStatus());

            return new JsonModel(['web_hook' => true]);

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