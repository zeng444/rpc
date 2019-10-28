<?php

namespace Services\User;

use Services\BaseDemo;

class Profile extends BaseDemo
{

    public function getById(int $id)
    {
        $sql = "SELECT id,realname from `consumer` WHERE id =:id limit 1";
        $result = $this->db->query($sql, ['id' => $id]);
        $result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $result->fetch();
    }
}
