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

    /**
     * @param $LineUserID
     */
    function setLineUserID($LineUserID)
    {
        $this->lineUserID = $LineUserID;
    }

    function getUserData()
    {
        if (empty($this->lineUserID)) {
            return null;
        }

        $sqlText = 'SELECT * FROM line_users WHERE id_line_users = "' . $this->lineUserID . '" LIMIT 1';
        $sql = $this->adapter->query($sqlText);
        $results = $sql->execute();
        return $results->current();
    }

    function saveNewUser()
    {
        $return = 0;
        try {
            if (empty($this->lineUserID)) {
                return null;
            }

            $id = $this->getNextID();
            $sqlText = "INSERT INTO line_users ( id , id_line_users, added_date, last_update)
                         VALUES  ( " . $id . ",'" . $this->lineUserID . "', NOW(), NOW())";

            $sql = $this->adapter->query($sqlText);
            if ($sql->execute()) {
                $return = $id;
            }
        } catch (\Exception $e) {
            $return = 0;
        }
        return $return;
    }

    function updateUserRestaurant(int $restaurantID)
    {
        if (empty($this->lineUserID)) {
            return null;
        }

        $sqlText = "UPDATE line_users SET restaurant_id = " . $restaurantID . " WHERE id_line_users = '" . $this->lineUserID . "'; ";
        $sql = $this->adapter->query($sqlText);
        $sql->execute();
    }

    function getNextID()
    {
        $sql = $this->adapter->query("SELECT MAX(id)+1 as id FROM `line_users` LIMIT 1");
        $results = $sql->execute();
        $row = $results->current();
        $id = $row['id'];
        if ($id == NULL) $id = 1;
        return ($id);
    }

}
