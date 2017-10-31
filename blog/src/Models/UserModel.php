<?php 
namespace Blog\Models;

use PDO;
use Blog\Exceptions\UnableToCreateUserException;

class UserModel extends AbstractModel 
{

    public function addUser (
        string $firstname,
        string $surename,
        string $email, 
        string $username,
        string $password,
        string $type = "user") {
    
    
        $this->db->beginTransaction();
       
        try {
            $query = 'INSERT INTO users(firstname, surename, email, username, password, type) VALUES (:firstname, :surename, :email, :username, :password, :type)';
            $statement = $this->db->prepare($query);
           
            $statement->bindValue("firstname",$firstname);
            $statement->bindValue("surename",$surename);
            $statement->bindValue("email",$email);
            $statement->bindValue("username",$username);
            $statement->bindValue("password",$password);
            $statement->bindValue("type",$type);
            
            if(!$statement->execute()) 
            {
                throw new UnableToCreateUserException();
            }
            
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function editUserType(string $username, string $type) 
    {
        $this->db->beginTransaction();
        try {
        $query = 'UPDATE users SET type = :type WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue("username", $username);
        $statement->bindValue("type", $type);
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }
        $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function get(int $userId): UserFactory
    {
        $query = 'SELECT * FROM users WHERE id = :id';
        $statement = $this->db->prepare($query);
        $statement->execute(['id' = $customerId]);
        $users = $statement->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        if(empty($users)) {
            throw new NotFoundException();
        }
        return $users[0];
    }

}

    


?>