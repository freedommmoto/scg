<?php

namespace Application\Models;

use Zend\Db\Adapter\Adapter;

class Restaurants
{
    protected $apies;

    function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
        $this->config = include __DIR__ . '../../../../config/module.config.php';
        $this->adapter = new Adapter($this->config['Db']);
    }

    function getList()
    {
        $text = '       [{
                            "type": "postback",
                            "label": "ร้านคุ้มอีสาน",
                            "data": 1
                          },
                          {
                            "type": "postback",
                            "label": "ร้านอาหารเจ เวจจี้ดิช",
                            "data": 2
                          },
                          {
                            "type": "postback",
                             "label": "About Beef",
                            "data": 3
                          },
                          {
                            "type": "postback",
                             "label": "ฉันยังไม่หิว",
                            "data": "none"
                          }]';

        return json_decode($text, true);
    }

    function getDetails($id): array
    {
        return ['name' => 'โกอ่างโภชนา สวนสน',
            'id' => 'eda48d4f977af7a455eca1eea383759510460e2d',
            'address' => "1128/10 ถนน ประชาราษฏร์ สาย1, บางซื่อ, Bangkok 10800, Thailand", //formatted_address
            'img' => 'https://lh3.googleusercontent.com/p/AF1QipM-AuigFwtqReObR2B_-XB8ng8ip0cBatFGv8QP=s1600-w1024'
        ];
    }

    /**
     * @param array $inputArray
     * @return int
     */
    function insert(array $inputArray): int
    {
        $return = 0;
        try {
            $id = $this->getNextID();
            $sqlText = "INSERT INTO line_log ( id , input_json, added_date, last_update) 
                        VALUES  ( " . $id . ",'" . json_encode($inputArray) . "', NOW(), NOW())";
            file_put_contents('output.txt', $sqlText . PHP_EOL, FILE_APPEND);
            $sql = $this->adapter->query($sqlText);
            if ($sql->execute()) {
                $return = $id;
            }
        } catch (\Exception $e) {
            $return = 0;
        }
        return $return;
    }

    /**
     * @param int $id
     * @param array $outputArray
     * @param int $outputHttpStatus
     * @return bool
     */
    function update(int $id, array $outputArray, int $outputHttpStatus): bool
    {
        try {
            $sqlText = "UPDATE line_log SET output_json = '" . json_encode($outputArray) . "' 
                , last_update = NOW() 
                , output_status = " . $outputHttpStatus . "
                WHERE id = " . $id . ";";
            file_put_contents('output.txt', $sqlText . PHP_EOL, FILE_APPEND);
            $sql = $this->adapter->query($sqlText);

            if ($sql->execute()) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    function getNextID()
    {
        $sql = $this->adapter->query("SELECT MAX(id)+1 as id FROM `line_log` LIMIT 1");
        $results = $sql->execute();
        $row = $results->current();
        $id = $row['id'];
        if ($id == NULL) $id = 1;
        return ($id);
    }

}
