<?php

namespace Blog\Models;

use PDO;
use Blog\Domain\Blogpost;
use Blog\Domain\Comment;

class BlogpostModel extends AbstractModel 
{

    const BLOGPOSTCLASSNAME = '\Blog\Domain\Blogpost';
    const COMMENTCLASSNAME ='\Blog\Domain\Comment';

    public function insertBlogPostToDb(int $userId, string $postName, string $content, array $tags)
    {
       
        $this->db->beginTransaction();
    
        try {
        
            $this->insertBlogpostInfoToDB($userId, $postName);
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
        $query = 'INSERT INTO blogposts_info(user_id, post_name, post_creation_time) VALUES(:user_id, :post_name, NOW())';
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
            
                if ($tag == $curentTags["tagname"]) 
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

    public function getBlogpostsByPage(int $page, int $pageLength):array 
    {
        $start = $pageLength * ($page -1);
       
        $query = 'SELECT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LIMIT :page,:length';
        $statement = $this->db->prepare($query);
       
        $statement->bindParam('page', $start, PDO::PARAM_INT);
        $statement->bindParam('length', $pageLength, PDO::PARAM_INT);
      
        $statement->execute();
        
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        return $result;
    }

    public function getAllBlogposts():array 
    {
       
        $query = 'SELECT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id';
        $statement = $this->db->prepare($query);
       
        $statement->execute();
        
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        return $result;
    }

    public function getTagsformPost(int $id)
    {
        $query = 'SELECT t.tagname FROM tags t LEFT JOIN post_tag_correspondens ptc ON t.id = ptc.tag_id LEFT JOIN blogposts_info bi ON ptc.post_id = bi.id WHERE bi.id =:id';
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();

        $tags = $statement->fetchAll();
        $result = "";
        foreach($tags as $tag)
        {
            $result .=  $tag['tagname'] . " ";
          
        }
        
        $result = trim($result);
    
        return $result;
    }

    public function getBlogpost(int $id) 
    {
        $query = 'SELECT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id WHERE bi.id =:id';
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id,PDO::PARAM_INT);

        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME)[0];
        return $result;

    }

    public function getByUserWithPage(int $userId, int $page, int $pageLength):array {
        $start = $pageLength * ($page-1);
       
        $query = 'SELECT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id WHERE bi.user_id = :id LIMIT :start,:length';
       
        $statement = $this->db->prepare($query);
    
        $statement->bindParam('id', $userId, PDO::PARAM_INT);
        $statement->bindParam('start', $start, PDO::PARAM_INT);
        $statement->bindParam('length', $pageLength, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        return $result;

    }
    public function getAllByUser(int $userId):array {
       
        $query = 'SELECT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id WHERE bi.user_id = :id';
       
        $statement = $this->db->prepare($query);
    
        $statement->bindParam('id', $userId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        return $result;

    }
    public function editBlogpost(int $blogpostId, string $postName, array $tags, string $content) {
        $this->db->beginTransaction();
        try {

        
            $query = 'UPDATE blogposts_info SET post_name =:post_name WHERE id =:blogpost_id';
            $statement = $this->db->prepare($query);
            $statement->bindValue('blogpost_id', $blogpostId);
            $statement->bindValue('post_name', $postName);
            if (!$statement->execute()) {
                
                throw new Exception($statement->errorinfo()[2]);;
            }
            
            $query = 'UPDATE blogposts_content SET content =:content WHERE id =:blogpost_id';
            $statement = $this->db->prepare($query);
            $statement->bindValue('blogpost_id', $blogpostId);
            $statement->bindValue('content', $content);
            if (!$statement->execute()) {
                throw new Exception($statement->errorinfo()[2]);;
                
            }
        
            $this->insertTagsToDb($tags);
        
            $query = 'DELETE FROM post_tag_correspondens WHERE post_id =:blogpost_id';
            $statement = $this->db->prepare($query);
            $statement->bindValue("blogpost_id", $blogpostId);
            if(!$statement->execute()) 
            {
                throw new Exception($statement->errorinfo()[2]);
            }

            $this->createTagPostConnection($tags, $blogpostId);

            $this->db->commit();

        } catch(Exception $e) {

            $this->db->rollBack();
            throw $e;
        }

    }

    public function searchByTags(string $tagname, int $page = null, int $pageLength = null) {
       
        $query = 'SELECT id FROM tags WHERE tagname =:tagname';
        $statement = $this->db->prepare($query);
        $statement->bindValue("tagname", $tagname);
        $statement->execute();
        $tagId = (int) $statement->fetch(PDO::FETCH_NUM)[0];
    
        if (isset($page)) {
         
            $start = ($page -1) * $pageLength;
           
            $query = 'SELECT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE ptc.tag_id =:tag_id LIMIT :start, :length';
         
            $statement = $this->db->prepare($query);
            $statement->bindValue("tag_id", $tagId, PDO::PARAM_INT);
            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);
           
        } else {
            $query = 'SELECT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE ptc.tag_id =:tag_id';
            $statement = $this->db->prepare($query);
            $statement->bindValue("tag_id", $tagId);
        }

        if(!$statement->execute()) 
        {

            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        return $result;
    }
    public function searchByPost(string $postName, int $page = null, int $pageLength = null) {
        if (isset($page)) {
        
            $start = ($page -1) * $pageLength;

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bi.post_name  LIKE :post_name LIMIT :start, :length';
            $statement = $this->db->prepare($query);
            $statement->bindValue("post_name", "%$postName%");
            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);
        } else {
            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bi.post_name  LIKE :post_name';
            $statement = $this->db->prepare($query);
            $statement->bindValue("post_name", "%$postName%");
        } 
       
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        return $result;
    }

    public function searchByContent(string $content, int $page = null, int $pageLength = null) {

        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bc.content LIKE :content LIMIT :start, :length';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("content", "%$content%", PDO::PARAM_STR);
            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);
    
        } else {

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bc.content LIKE :content';
        
            $statement = $this->db->prepare($query);

            $statement->bindValue("content", "%$content%", PDO::PARAM_STR);
        }

        if(!$statement->execute()) {
            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

      
        return $result;
    }

    public function searchByTagsAndContent(string $search, int $page = null, int $pageLength = null) {

        $query = 'SELECT id FROM tags WHERE tagname =:tagname';
        $statement = $this->db->prepare($query);
        $statement->bindValue("tagname", $search);
        $statement->execute();
        $tagId = $statement->fetch(PDO::FETCH_NUM)[0];

        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bc.content LIKE :content OR  ptc.tag_id = :tag_id LIMIT :start, :length';
        
            $statement = $this->db->prepare($query);

            $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("tag_id", $tagId, PDO::PARAM_INT);
            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);

        } else {

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bc.content LIKE :content OR  ptc.tag_id = :tag_id';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("tag_id", $tagId, PDO::PARAM_INT);
        }

        if(!$statement->execute()) {
            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

      
        return $result;
    }

    public function searchByPostAndContent (string $search, int $page = null, int $pageLength = null) {

        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bc.content LIKE :content OR  bi.post_name LIKE :post_name LIMIT :start, :length';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);
        } else {
            
            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bc.content LIKE :content OR  bi.post_name LIKE :post_name';
        
            $statement = $this->db->prepare($query);

            $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);
        }
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        
        return $result;
    }

    public function searchByTagsAndPost (string $search, int $page = null, int $pageLength = null) {

        $query = 'SELECT id FROM tags WHERE tagname =:tagname';
        $statement = $this->db->prepare($query);
        $statement->bindValue("tagname", $search);
        $statement->execute();
        $tagId = $statement->fetch(PDO::FETCH_NUM)[0];

        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bi.post_name LIKE :post_name OR ptc.tag_id = :tag_id LIMIT :start, :length';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("tag_id", $tagId, PDO::PARAM_INT);
            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);

        } else {

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bi.post_name LIKE :post_name OR ptc.tag_id = :tag_id';

            $statement = $this->db->prepare($query);

            $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("tag_id", $tagId, PDO::PARAM_INT);
        }

        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        
        return $result;
    }

    public function searchByTagsPostAndContent (string $search, int $page = null, int $pageLength = null) {
        
        $query = 'SELECT id FROM tags WHERE tagname =:tagname';
        $statement = $this->db->prepare($query);
        $statement->bindValue("tagname", $search);
        $statement->execute();
        $tagId = $statement->fetch(PDO::FETCH_NUM)[0];

        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bi.post_name LIKE :post_name OR bc.content LIKE :content OR ptc.tag_id = :tag_id LIMIT :start, :length';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("tag_id", $tagId, PDO::PARAM_INT);
            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);

        } else {

            $query = 'SELECT DISTINCT bi.*, bc.content, u.username FROM blogposts_info bi LEFT JOIN blogposts_content bc ON bi.id = bc.id LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN post_tag_correspondens ptc ON bi.id = ptc.post_id WHERE bi.post_name LIKE :post_name OR bc.content LIKE :content OR ptc.tag_id = :tag_id';

            $statement = $this->db->prepare($query);
            $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
            $statement->bindValue("tag_id", $tagId, PDO::PARAM_INT);
        }

        if(!$statement->execute()) 
        {

            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        
        return $result;
    }

    public function deletePostFromDb(int $id) {
        $this->db->beginTransaction();
        
        try {

            $query = 'DELETE FROM blogposts_comments WHERE post_id =:id';
            $statement = $this->db->prepare($query);
            $statement->bindValue("id", $id);
            
            if(!$statement->execute()) 
            {
                throw new Exception($statement->errorinfo()[2]);
            }
    

            $query = 'DELETE FROM blogposts_content WHERE id =:id';
            $statement = $this->db->prepare($query);
            $statement->bindValue("id", $id);
            
            if(!$statement->execute()) 
            {
                throw new Exception($statement->errorinfo()[2]);
            }

            $query = 'DELETE FROM post_tag_correspondens WHERE post_id =:blogpost_id';
            $statement = $this->db->prepare($query);
            $statement->bindValue("blogpost_id", $id);

            if(!$statement->execute()) 
            {
                throw new Exception($statement->errorinfo()[2]);
            }

            $query = 'DELETE FROM blogposts_info WHERE id =:id';
            $statement = $this->db->prepare($query);
            $statement->bindValue("id", $id);
         
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

    public function insertCommentToDb(int $userId, int $blogpostId, string $content)
    {
        $query = 'INSERT INTO blogposts_comments(post_id, content, user_id, post_creation_time) VALUES (:post_id, :content, :user_id, NOW())';
        $statement = $this->db->prepare($query);
        $statement->bindValue('post_id', $blogpostId);
        $statement->bindValue('content', $content);
        $statement->bindValue('user_id', $userId);
       
        if(!$statement->execute()) 
        {     
            throw new Exception($statement->errorinfo()[2]);
        }       
        
        $statement->execute();

    }

    public function getComments(int $id)
    {
        $query ='SELECT bc.* , u.username FROM blogposts_comments bc LEFT JOIN users u ON u.id = bc.user_id WHERE bc.post_id = :post_id';
        $statement = $this->db->prepare($query);
        $statement->bindValue('post_id', $id);


        if(!$statement->execute()) 
        {     
            throw new Exception($statement->errorinfo()[2]);
        } 

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::COMMENTCLASSNAME);
        return $result;

    }

    public function deleteCommentFromDb(int $id)
    {
        $query = 'DELETE FROM blogposts_comments WHERE id =:id';
        $statement = $this->db->prepare($query);
        $statement->bindValue("id", $id);
        
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
    }

    public function getComment(int $id)
    {
        $query ='SELECT bc.* , u.username FROM blogposts_comments bc LEFT JOIN users u ON u.id = bc.user_id WHERE bc.id = :id';
        $statement = $this->db->prepare($query);
        $statement->bindValue('id', $id);


        if(!$statement->execute()) 
        {     
            throw new Exception($statement->errorinfo()[2]);
        } 

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::COMMENTCLASSNAME);
        return $result;

    }

    public function editComment(int $id, string $content) 
    {
        $query = 'UPDATE blogposts_comments SET content =:content WHERE id =:id';
        $statement = $this->db->prepare($query);
        $statement->bindValue('id', $id);
        $statement->bindValue('content', $content);
        if (!$statement->execute()) {
            throw new Exception($statement->errorinfo()[2]);;
            
        }
        
        $statement->execute();
    
    }
    public function topTags() : array
    {
        
        $query = 'SELECT DISTINCT t.*, COUNT(ptc.tag_id) AS count FROM tags t LEFT JOIN post_tag_correspondens ptc ON ptc.tag_id = t.id GROUP BY t.tagname ORDER BY count DESC LIMIT 3';
        $statement = $this->db->prepare($query);
       

        if (!$statement->execute()) {
            throw new Exception($statement->errorinfo()[2]);;
            
        }
       
        $statement->execute();

        $result = $statement->fetchAll();

        return $result;
        
    }
}
?>