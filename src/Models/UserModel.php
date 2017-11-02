<?php 
namespace Blog\Models;

use PDO;
use Blog\Exceptions\UnableToCreateUserException;
use Blog\Domain\User\UserFactory;
use Blog\Domain\User;

class UserModel extends AbstractModel 
{
    const CLASSNAME = '\Blog\Domain\User\UserFactory';

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
    
    public function get(int $userId): User
    {
        $query = 'SELECT * FROM users WHERE id = :id';
        $statement = $this->db->prepare($query);
        $statement->execute(['id' => $userId]);
        $users = $statement->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        if(empty($users)) {
            throw new NotFoundException();
        }
        return $users[0];
    }

    public function getByUsername(string $username): User
    {
        
        $query = 'SELECT * FROM users WHERE username =:username';
        $statement = $this->db->prepare($query);
       
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();
        if(empty($user)) {
            
            throw new NotFoundException();
        }
       
        return new User(
            $user['type'],
            $user['id'],
            $user['firstname'],
            $user['surename'],
            $user['username'],
            $user['password'],
            $user['email']

        );
    }

}

    


?>