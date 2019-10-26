<?php

namespace Application\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class Restaurants
{
    protected $apies;

    function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
        $this->config = include __DIR__ . '../../../../config/module.config.php';
        $this->adapter = new Adapter($this->config['Db']);
    }

    function getList(): array
    {
        $sql = $this->adapter->query('SELECT * FROM restaurants ORDER BY RAND() LIMIT 3');
        $results = $sql->execute();

        $resultSet = new ResultSet;
        $rs = $resultSet->initialize($results);
        $rows = $rs->toArray();

        if (!empty($rows)) {
            $results = [];
            foreach ($rows as $row) {
                $result = [];
                $result['type'] = 'postback';
                $result['label'] = $row['name'];
                $result['data'] = (int)$row['id'];
                $results[] = $result;
            }
            $results[] = ['type' => 'postback', 'label' => 'ฉันยังไม่หิว', 'data' => 0];

            return $results;
        }

//        $text = '       [{
//                            "type": "postback",
//                            "label": "ร้านคุ้มอีสาน",
//                            "data": 1
//                          },
//                          {
//                            "type": "postback",
//                            "label": "ร้านอาหารเจ เวจจี้ดิช",
//                            "data": 2
//                          },
//                          {
//                            "type": "postback",
//                             "label": "About Beef",
//                             "data": 3
//                          },
//                          {
//                            "type": "postback",
//                             "label": "ฉันยังไม่หิว",
//                            "data": 0
//                          }]';
//
//        return json_decode($text, true);
    }

    /**
     * @param int $id
     * @return array
     */
    function getDetails(int $id): array
    {
        $sql = $this->adapter->query("SELECT * FROM restaurants where id = '" . $id . "' LIMIT 1");
        $results = $sql->execute();

        $resultSet = new ResultSet;
        $rs = $resultSet->initialize($results);
        $row = $rs->toArray();

        if (isset($row[0]['name'])) {
            $row = $row[0];
            return [
                'name' => $row['name'],
                'id' => $id,
                'address' => $row['address'],
                'img' => $row['img'],
            ];
        }

        return [
            'name' => 'โกอ่างโภชนา สวนสน',
            'id' => 1,
            'address' => "1128/10 ถนน ประชาราษฏร์ สาย1, บางซื่อ, Bangkok 10800, Thailand", //formatted_address
            'img' => 'https://lh3.googleusercontent.com/p/AF1QipM-AuigFwtqReObR2B_-XB8ng8ip0cBatFGv8QP=s1600-w1024'
        ];
    }

    /**
     * @param array $restaurantsList
     */
    function saveRestaurants(array $restaurantsList): void
    {
        try {
            /**
             * create table restaurants
             * (
             * id          integer primary key auto_increment,
             * google_id   varchar(90) not null,
             * name        varchar(120) default null,
             * address     text,
             * img         text,
             * added_date  datetime default null,
             * last_update datetime default null
             * );
             * );
             */

            foreach ($restaurantsList as $restaurant) {
                if (!isset($restaurant['id']) || !isset($restaurant['loaded_picture'])) {
                    continue;
                }

                if (empty($restaurant['id']) || empty($restaurant['loaded_picture'])) {
                    continue;
                }

                $sql = $this->adapter->query("SELECT * FROM restaurants where google_id = '" . $restaurant['id'] . "' LIMIT 1");
                $results = $sql->execute();
                if (!empty($results->current())) {
                    continue;
                }

                $id = $this->getNextID();
                $sqlText = "INSERT INTO restaurants ( id , google_id, name, address , img , added_date , last_update) 
                VALUES  ( " . $id . "
                    ,'" . $restaurant['id'] . "'
                    ,'" . $restaurant['name'] . "'
                    ,'" . $restaurant['formatted_address'] . "'
                    ,'" . $restaurant['loaded_picture'] . "'
                    , NOW()
                    , NOW()
                    )";

                $sql = $this->adapter->query($sqlText);
                $sql->execute();

            }

        } catch (\Exception $e) {
            //Exception
        }

    }

    /**
     * @return int
     */
    function getNextID(): int
    {
        $sql = $this->adapter->query("SELECT MAX(id)+1 as id FROM `restaurants` LIMIT 1");
        $results = $sql->execute();
        $row = $results->current();
        $id = $row['id'];
        if ($id == NULL) $id = 1;
        return ($id);
    }

}
