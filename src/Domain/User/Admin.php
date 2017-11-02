<?php 

namespace Blog\Domain\User;


class Admin extends User 
{
    public function getMessage() {
        echo "är det det här som saknas?";
    }
 }
