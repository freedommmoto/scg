<?php

namespace Application\Models;

use Zend\Db\Adapter\Adapter;

class LineUser
{
    protected $apies;

    function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
        $this->config = include __DIR__ . '../../../../config/module.config.php';
        $this->adapter = new Adapter($this->config['Db']);
    }

    function getUserData(array $inputArray)
    {
        return null;
    }

    /**
     * @param array $inputArray
     * @return int
     */
    function saveNewUser(array $inputArray): void
    {
        return;
//        $return = 0;
//        try {
//            $id = $this->getNextID();
//            $sqlText = "INSERT INTO line_log ( id , input_json, added_date, last_update)
//                        VALUES  ( " . $id . ",'" . json_encode($inputArray) . "', NOW(), NOW())";
//            file_put_contents('output.txt', $sqlText . PHP_EOL, FILE_APPEND);
//            $sql = $this->adapter->query($sqlText);
//            if ($sql->execute()) {
//                $return = $id;
//            }
//        } catch (\Exception $e) {
//            $return = 0;
//        }
//        return $return;
    }

    function updateUserData(array $inputArray,bool $ordering): void
    {
        return;
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
