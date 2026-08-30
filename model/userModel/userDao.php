<?php

namespace model\userModel;

use model\db\connector;
use model\mainClasses\role;
use model\mainClasses\users;

class UserDao{

    public function createUser(users $users){
        $conn = new connector();
        $conn->getConnection();
    }
}