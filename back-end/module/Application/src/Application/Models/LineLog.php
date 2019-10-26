<?php

namespace Application\Models;

use Zend\Db\Adapter\Adapter;

class LineLog
{
    protected $apies;

    function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
        $this->config = include __DIR__ . '../../../../config/module.config.php';
        $this->adapter = new Adapter($this->config['Db']);
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
