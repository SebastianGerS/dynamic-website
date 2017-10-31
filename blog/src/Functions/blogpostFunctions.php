<?php use Blog\Utils\Connection;

function newBlogPost(int $userId, string $postName, string $content, array $tags)
{
    $db = Connection::getInstance();
    $db->handler->beginTransaction();

    try {

        $query = 'INSERT INTO blogposts_info(user_id, post_name, post_time) VALUES(:user_id, :post_name, NOW())';
        $statement = $db->handler->prepare($query);
        $statement->bindValue("user_id", $userId);
        $statement->bindValue("post_name", $postName);
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }


        $postId = $db->handler->lastInsertId("blogposts_info");
        $query = 'INSERT INTO blogposts_content(id, content) VALUES (:id , :content)';
        $statement = $db->handler->prepare($query);
        $statement->bindValue("id", $postId);
        $statement->bindValue("content", $content);
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }

        
        $query = 'SELECT tagname FROM tags';
        $statement = $db->handler->prepare($query);
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
                $statement = $db->handler->prepare($query);
                $statement->bindValue("tagname", $tag);
                if(!$statement->execute()) 
                {
                    throw new Exception($statement->errorinfo()[2]);
                }
            }
        }

        
       foreach($tags as $tag) {
            $query = 'SELECT id FROM tags WHERE tagname =:tagname';
            $statement = $db->handler->prepare($query);
            $statement->bindValue("tagname", $tag);
            if(!$statement->execute()) 
            {
                throw new Exception($statement->errorinfo()[2]);
            }
            $statement->execute();
            $tagId = $statement->fetch(PDO::FETCH_NUM)[0];
            $query = 'INSERT INTO post_tag_correspondens(post_id, tag_id) VALUES (:post_id, :tag_id)';
            $statement = $db->handler->prepare($query);
            $statement->bindValue("post_id", $postId);
            $statement->bindValue("tag_id", $tagId);
            if(!$statement->execute()) 
            {
                throw new Exception($statement->errorInfo()[2]);
                
            }
        }
        $db->handler->commit();
    } catch (Exception $e) {
        $db->handler->rollBack();
        throw $e;
    }

}

function createBlogPostInfo() {

    
}


?>
