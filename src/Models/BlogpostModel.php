<?php

namespace Blog\Models;

use PDO;
use Blog\Domain\Blogpost;

class BlogpostModel extends AbstractModel 
{

    const CLASSNAME = '\Blog\Domain\Blogpost';

    public function insertBlogPostToDb(int $userId, string $postName, string $content, array $tags)
    {
        $this->db->beginTransaction();
    
        try {
            $this->insertBlogpostInfoToDB($userId, $content);
            $postId = $this->insertBlogpostContentToDb($userId, $content);
            $this->insertTagsToDb($tags);
            $this->createTagPostConnection($tags, $postId);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    
    }
    
    protected function insertBlogpostInfoToDB(int $userId, string $postName) {
        $query = 'INSERT INTO blogposts_info(user_id, post_name, post_time) VALUES(:user_id, :post_name, NOW())';
        $statement = $this->db->prepare($query);
        $statement->bindValue("user_id", $userId);
        $statement->bindValue("post_name", $postName);
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }
    }

    protected function insertBlogpostContentToDb(int $userId, string $content) {
        $postId = $this->db->lastInsertId("blogposts_info");
        $query = 'INSERT INTO blogposts_content(id, content) VALUES (:id ,:content)';
        $statement = $this->db->prepare($query);
        $statement->bindValue("id", $postId);
        $statement->bindValue("content", $content);
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }

        return $postId;
    }

    protected function insertTagsToDb(array $tags) {
        $query = 'SELECT tagname FROM tags';
        $statement = $this->db->prepare($query);
        $statement->execute();
        $taglists = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach($tags as $tag) 
        {
            $toAdd = true;
            foreach($taglists as $curentTags)
            {
                if ($tag == $curentTags[tagname]) 
                {
                $toAdd = false;  
                }
            } 
            if($toAdd)
            {
                $query = 'INSERT INTO tags(tagname) VALUES (:tagname)';
                $statement = $this->db->prepare($query);
                $statement->bindValue("tagname", $tag);
                if(!$statement->execute()) 
                {
                    throw new Exception($statement->errorinfo()[2]);
                }
            }
        }
    }

    protected function createTagPostConnection (array $tags, int $postId) {
        
        foreach($tags as $tag) {
            
            $query = 'SELECT id FROM tags WHERE tagname =:tagname';
            $statement = $this->db->prepare($query);
            $statement->bindValue("tagname", $tag);
            if(!$statement->execute()) 
            {
                throw new Exception($statement->errorinfo()[2]);
            }
            $statement->execute();
            $tagId = $statement->fetch(PDO::FETCH_NUM)[0];
            $query = 'INSERT INTO post_tag_correspondens(post_id, tag_id) VALUES (:post_id, :tag_id)';
            $statement = $this->db->prepare($query);
            $statement->bindValue("post_id", $postId);
            $statement->bindValue("tag_id", $tagId);
            if(!$statement->execute()) 
            {
                throw new Exception($statement->errorInfo()[2]);
                
            }
        }
    }

    public function getAllBlogposts() {
        
        $query = 'SELECT bi.id, bi.user_id, bi.post_time, bi.post_name, bc.content FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id';
        
        $statement = $this->db->prepare($query);
        
        $statement->execute();
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        return $result;
    }
}
?>