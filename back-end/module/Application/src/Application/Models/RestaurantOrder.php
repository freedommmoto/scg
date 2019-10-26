<?php

namespace Application\Models;

use Zend\Db\Adapter\Adapter;

class RestaurantOrder
{
    protected $apies;

    function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
        $this->config = include __DIR__ . '../../../../config/module.config.php';
        $this->adapter = new Adapter($this->config['Db']);
    }

    /**
     * @param array $userData
     * @param $orderName\
     */
    function insert(array $userData, $orderName): void
    {

        /**
         * create table restaurant_order
         * (
         * id            integer primary key auto_increment,
         * restaurant_id integer,
         * order_text    text,
         * user_id       int      default null,
         * added_date    datetime default null,
         * last_update   datetime default null
         * );
         */

        try {
            $id = $this->getNextID();
            $sqlText = "INSERT INTO restaurant_order ( id ,restaurant_id,order_text,user_id, added_date, last_update) 
                        VALUES  ( " . $id . ","
                . $userData['restaurant_id'] . ","
                . "'" . $orderName . "' ,"
                . $userData['id'] . ","
                . "NOW() ,
                   NOW())";

            $sql = $this->adapter->query($sqlText);
            $sql->execute();
        } catch (\Exception $e) {
            //
        }
    }

    function getNextID()
    {
        $sql = $this->adapter->query("SELECT MAX(id)+1 as id FROM `restaurant_order` LIMIT 1");
        $results = $sql->execute();
        $row = $results->current();
        $id = $row['id'];
        if ($id == NULL) $id = 1;
        return ($id);
    }

}
