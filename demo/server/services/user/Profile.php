<?php

namespace Services\User;

use Core\ServiceBase;

class Profile extends ServiceBase
{

    public function getById(int $id)
    {
        $sql = "SELECT id,realname from `consumer` WHERE id =:id limit 1";
        $result = $this->db->query($sql, ['id' => $id]);
        $result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $result->fetch();
    }
}
