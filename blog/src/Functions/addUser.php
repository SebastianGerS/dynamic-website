<?php 

function addUser (
    string $firstname,
    string $surename,
    string $email, 
    string $username,
    string $password,
    string $type = "user") {

    $db = Connection::getInstance();
    $db->handler->beginTransaction();
    try {
        $query = 'INSERT INTO users(firstname, surename, email, username, password, type) VALUES (:firstname, :surename, :email, :username, :password, :type)';
        $statement = $db->handler->prepare($query);
        $statement->bindValue("firstname",$firstname);
        $statement->bindValue("surename",$surename);
        $statement->bindValue("email",$email);
        $statement->bindValue("username",$username);
        $statement->bindValue("password",$password);
        $statement->bindValue("type",$type);
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }
        $db->handler->commit();
    } catch (Exception $e) {
        $db->handler->rollBack();
        throw $e;
    }
}
    


?>