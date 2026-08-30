<?php

namespace model\userModel;

use model\db\connector;
use model\mainClasses\role;

class UserDao{

    public function createUser(string $username, string $password, string $email, role $role){
        $conn = new connector();
    }
}