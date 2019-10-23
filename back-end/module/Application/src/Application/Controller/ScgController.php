<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\Adapter;
use Application\Models\Users;
use Zend\View\Model\JsonModel;
use Zend\Cache\StorageFactory;

class ScgController extends AbstractActionController
{
################################################################################ 
    public function __construct()
    {
        $this->cacheTime = 36000;
        $this->now = date('Y-m-d H:i:s');
        $this->config = include __DIR__ . '../../../../config/module.config.php';
        $this->adapter = new Adapter($this->config['Db']);
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


################################################################################
    public function indexAction()
    {
        try {
            return new JsonModel([
                'status' => 'SUCCESS',
                'message' => 'Here is your data',
                'data' => [
                    'full_name' => 'John Doe',
                    'address' => '51 Middle st.'
                ]
            ]);

        } catch (Exception $e) {
            print_r($e);
        }
    }

################################################################################
    public function testAction()
    {
        try {
            $models = new Users($this->adapter, 1, 1);
            $data = $models->getList();
            return new JsonModel([
                $data,
                'status' => getenv('DB_USERNAME'),
            ]);

        } catch (Exception $e) {
            print_r($e);
        }
    }

################################################################################
    public function findXYZAction()
    {
        try {
            $cache = $this->_initCache();
            $cacheKey = 'findXYZ';
            //$cache->removeItems([$cacheKey]);

            $data = $cache->getItem($cacheKey);

            if (empty($data)) {
                $data = $this->findXYZ();
                $cache->setItem($cacheKey, $data);
            }

            return new JsonModel(
                ['XYZ' => $data]
            );

        } catch (Exception $e) {
            print_r($e);
        }
    }

################################################################################
    private function findXYZ()
    {
        $sumPerRound = 2;
        $maxPosition = 6;
        $minPosition = 0;

        $baseValue = 9;
        $startPosition = 2;

        $arrayNum = range($minPosition, $maxPosition);
        $arrayNum[$startPosition] = $baseValue;

        foreach ($arrayNum as $position => $number) {
            if ($position < $startPosition) {
                $arrayNum[$position] = $baseValue - ($sumPerRound * ($startPosition - ($position - 1)));
            } elseif ($position > $startPosition) {
                $arrayNum[$position] = $arrayNum[$position - 1] + ($sumPerRound * $position);
            }
        }

        return $arrayNum;
    }
}