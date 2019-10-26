<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Application\Models\Users;
use Application\Models\LineLog;
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
//            $lineUser = new lineUser();
            $lineApi = new LineBotApi();

//            $rOrder = new RestaurantOrder();
//            $restaurant = new Restaurants();

            $lineApi->testWebHook();
            $inputArray = $lineApi->getInput();
            $logID = $lineLog->insert($inputArray);


//            $userData = $lineUser->getUserData($inputArray);
//
//            if ($lineApi->checkUserIsOrder()) { // 1)
//                $restaurant->getList();
//                $lineApi->sendRestaurantsList();
//
//            } else if ($lineApi->checkUserIsSelectRestaurant()) { // 2)
//                $restaurant = $restaurant->getDetails();
//                $lineApi->sendRestaurantDetails($restaurant);        // 3)
//                $lineApi->askUserWhatDoYouWantForOrder($restaurant); // 3)
//                $userData->updateUserData($inputArray, true);
//
//            } else if ($userData['ordering']) { // 4)
//                $lineApi->sendThankyouMessage();
//                $rOrder->insert($userData, $inputArray);
//
//            } else { // 5)
//                $userData->updateUserData($inputArray, null);
//                $lineApi->sendNotUndestandMessage();
//            }

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