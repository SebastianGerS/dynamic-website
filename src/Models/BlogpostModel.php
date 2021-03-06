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
    
    protected function insertBlogpostInfoToDB(int $userId, string $postName) 
    {
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

    protected function insertTagsToDb(array $tags) 
    {
        $query = 'SELECT tagname FROM tags';
        $statement = $this->db->prepare($query);
        $statement->execute();
        $taglists = $statement->fetchAll(PDO::FETCH_ASSOC);
       
        foreach($tags as $tag) {
            
            $toAdd = true;

            foreach($taglists as $curentTags){
               
            
                if ($tag == $curentTags["tagname"]) {
                    
                    $toAdd = false;  
                }
            } 

            if($toAdd){   
               
                $query = 'INSERT INTO tags(tagname) VALUES (:tagname)';
                $statement = $this->db->prepare($query);
                $statement->bindValue("tagname", $tag);
             
                if(!$statement->execute()) {   
                    throw new Exception($statement->errorinfo()[2]);
                }
            }
        }
    }

    protected function createTagPostConnection (array $tags, int $postId) 
    {    
        foreach($tags as $tag) {
            
            $query = 'SELECT id FROM tags WHERE tagname =:tagname';
            $statement = $this->db->prepare($query);
            $statement->bindValue("tagname", $tag);
            if(!$statement->execute()) {
                throw new Exception($statement->errorinfo()[2]);
            }
            $statement->execute();
            $tagId = $statement->fetch(PDO::FETCH_NUM)[0];
            $query = 'INSERT INTO post_tag_correspondens(post_id, tag_id) VALUES (:post_id, :tag_id)';
            $statement = $this->db->prepare($query);
            $statement->bindValue("post_id", $postId);
            $statement->bindValue("tag_id", $tagId);
            if(!$statement->execute()) {
                throw new Exception($statement->errorInfo()[2]);
                
            }
        }
    }

    public function getBlogpostsByPage(int $page, int $pageLength):array 
    {
        $start = $pageLength * ($page -1);
        $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
        LEFT JOIN blogposts_content bc ON bc.id = bi.id 
        LEFT JOIN users u ON u.id = bi.user_id 
        LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
        LEFT JOIN tags t ON t.id = ptc.tag_id 
        GROUP BY bi.id 
        ORDER BY bi.post_creation_time DESC 
        LIMIT :page,:length';
        $statement = $this->db->prepare($query);
       
        $statement->bindParam('page', $start, PDO::PARAM_INT);
        $statement->bindParam('length', $pageLength, PDO::PARAM_INT);
      
        $statement->execute();
        
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        return $result;
    }

    public function getAllBlogposts():array 
    {
        $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
        LEFT JOIN blogposts_content bc ON bc.id = bi.id 
        LEFT JOIN users u ON u.id = bi.user_id 
        LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
        LEFT JOIN tags t ON t.id = ptc.tag_id 
        GROUP BY bi.id 
        ORDER BY bi.post_creation_time DESC';
        $statement = $this->db->prepare($query);
       
        $statement->execute();
        
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
       
        return $result;
    }

    public function getBlogpost(int $id) 
    {
        $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
        LEFT JOIN blogposts_content bc ON bc.id = bi.id 
        LEFT JOIN users u ON u.id = bi.user_id 
        LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
        LEFT JOIN tags t ON t.id = ptc.tag_id 
        WHERE bc.id =:id 
        GROUP BY bi.id';
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id,PDO::PARAM_INT);

        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME)[0];
        return $result;

    }

    public function getByUserWithPage(int $userId, int $page, int $pageLength):array 
    {
        $start = $pageLength * ($page-1);
       
        $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
        LEFT JOIN blogposts_content bc ON bc.id = bi.id 
        LEFT JOIN users u ON u.id = bi.user_id 
        LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
        LEFT JOIN tags t ON t.id = ptc.tag_id 
        WHERE bi.user_id = :id 
        GROUP BY bi.id 
        ORDER BY bi.post_creation_time DESC 
        LIMIT :start,:length';

        $statement = $this->db->prepare($query);
    
        $statement->bindParam('id', $userId, PDO::PARAM_INT);
        $statement->bindParam('start', $start, PDO::PARAM_INT);
        $statement->bindParam('length', $pageLength, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        return $result;

    }

    public function getAllByUser(int $userId):array 
    {
       
        $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname) AS tags FROM blogposts_info bi 
        LEFT JOIN blogposts_content bc ON bc.id = bi.id 
        LEFT JOIN users u ON u.id = bi.user_id 
        LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
        LEFT JOIN tags t ON t.id = ptc.tag_id 
        WHERE bi.user_id = :id 
        GROUP BY bi.id 
        ORDER BY bi.post_creation_time DESC';

        $statement = $this->db->prepare($query);
    
        $statement->bindParam('id', $userId, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        return $result;

    }

    public function editBlogpostInfo(int $postId, string $postName) 
    {

        $query = 'UPDATE blogposts_info SET post_name =:post_name, post_edit_time = NOW() WHERE id =:post_id';

        $statement = $this->db->prepare($query);

        $statement->bindValue('post_id', $postId);
        $statement->bindValue('post_name', $postName);

        if (!$statement->execute()) {
            
            throw new Exception($statement->errorinfo()[2]);;
        }
        
    }

    public function editBlogpostContent(int $postId, string $content) 
    {

        $query = 'UPDATE blogposts_content SET content =:content WHERE id =:post_id';
        $statement = $this->db->prepare($query);
        $statement->bindValue('post_id', $postId);
        $statement->bindValue('content', $content);
        if (!$statement->execute()) {
            throw new Exception($statement->errorinfo()[2]);;
            
        }
    }

    public function deletePostTagCorrespondens(int $postId) 
    {
        $query = 'DELETE FROM post_tag_correspondens WHERE post_id =:post_id';
        $statement = $this->db->prepare($query);
        $statement->bindValue("post_id", $postId);

        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }
    
    }

    public function editBlogpost(int $postId, string $postName, array $tags, string $content) 
    {
        $this->db->beginTransaction();

        try {

            $this->editBlogpostInfo($postId, $postName);
            $this->editBlogpostContent($postId, $content);
            $this->insertTagsToDb($tags);
            $this->deletePostTagCorrespondens($postId);
            $this->createTagPostConnection($tags, $postId);

            $this->db->commit();

        } catch(Exception $e) {

            $this->db->rollBack();
            throw $e;
        }

    }

    public function searchByTags(string $tagname, int $page = null, int $pageLength = null) {
       
        if (isset($page)) {
         
            $start = ($page -1) * $pageLength;
           
            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " " ) AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id
            GROUP BY bi.id 
            HAVING tags LIKE :tagname
            ORDER BY bi.post_creation_time DESC
            LIMIT :start, :length';

            $statement = $this->db->prepare($query);

            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);
           
        } else {
            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            GROUP BY bi.id 
            HAVING tags LIKE :tagname
            ORDER BY bi.post_creation_time DESC';

            $statement = $this->db->prepare($query);
        }

        $statement->bindValue("tagname", "%$tagname%");

        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        return $result;
    } // function that does on of two things dempending on the input. 
    //either it gives all the results matching a sertain word on tags
    //or it gets a limited part of that same result

    public function searchByPost(string $postName, int $page = null, int $pageLength = null) 
    {
        if (isset($page)) {
        
            $start = ($page -1) * $pageLength;

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            WHERE bi.post_name  LIKE :post_name 
            GROUP BY bi.id 
            ORDER BY bi.post_creation_time 
            DESC LIMIT :start, :length';

            $statement = $this->db->prepare($query);

            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);

        } else {
            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            WHERE bi.post_name  LIKE :post_name 
            GROUP BY bi.id 
            ORDER BY bi.post_creation_time DESC';

            $statement = $this->db->prepare($query);
        } 

        $statement->bindValue("post_name", "%$postName%");
       
        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        return $result;
    } // function that does on of two things dempending on the input. 
    //either it gives all the results matching a sertain word on post_name
    //or it gets a limited part of that same result

    public function searchByContent(string $content, int $page = null, int $pageLength = null) 
    {

        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            WHERE bc.content LIKE :content 
            GROUP BY bi.id 
            ORDER BY bi.post_creation_time DESC 
            LIMIT :start, :length';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);
    
        } else {

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            WHERE bc.content LIKE :content 
            GROUP BY bi.id 
            ORDER BY bi.post_creation_time DESC';
        
            $statement = $this->db->prepare($query);
        }

        $statement->bindValue("content", "%$content%", PDO::PARAM_STR);

        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
      
        return $result;
    }// function that does on of two things dempending on the input. 
    //either it gives all the results matching a sertain word on content
    //or it gets a limited part of that same result

    public function searchByTagsAndContent(string $search, int $page = null, int $pageLength = null) 
    {

        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            GROUP BY bi.id 
            HAVING tags LIKE :tagname OR bc.content LIKE :content
            ORDER BY bi.post_creation_time DESC 
            LIMIT :start, :length';
        
            $statement = $this->db->prepare($query);

            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);

        } else {

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            GROUP BY bi.id 
            HAVING tags LIKE :tagname OR bc.content LIKE :content
            ORDER BY bi.post_creation_time DESC';
            
            $statement = $this->db->prepare($query);
        }   

        $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
        $statement->bindValue("tagname", "%$search%", PDO::PARAM_INT);

        if(!$statement->execute()) {
            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
       
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        return $result;
    }// function that does on of two things dempending on the input. 
    //either it gives all the results matching a sertain word on aither content or tagname
    //or it gets a limited part of that same result

    public function searchByPostAndContent (string $search, int $page = null, int $pageLength = null) 
    {

        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;
            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            WHERE bc.content LIKE :content OR  bi.post_name LIKE :post_name 
            GROUP BY bi.id 
            ORDER BY bi.post_creation_time DESC 
            LIMIT :start, :length';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);

        } else {

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            WHERE bc.content LIKE :content OR  bi.post_name LIKE :post_name 
            GROUP BY bi.id 
            ORDER BY bi.post_creation_time DESC';
        
            $statement = $this->db->prepare($query);
        }

        $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
        $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);

        if(!$statement->execute()) {
            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        
        return $result;
    }// function that does on of two things dempending on the input. 
    //either it gives all the results matching a sertain word on aither post_name or content
    //or it gets a limited part of that same result

    public function searchByTagsAndPost (string $search, int $page = null, int $pageLength = null) 
    {
        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            GROUP BY bi.id 
            HAVING bi.post_name LIKE :post_name OR tags LIKE :tagname 
            ORDER BY bi.post_creation_time DESC 
            LIMIT :start, :length';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);

        } else {

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id 
            GROUP BY bi.id 
            HAVING bi.post_name LIKE :post_name OR tags LIKE :tagname 
            ORDER BY bi.post_creation_time DESC';

            $statement = $this->db->prepare($query);

        }

        $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);
        $statement->bindValue("tagname", "%$search%", PDO::PARAM_INT);

        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);
        
        return $result;
    }// function that does on of two things dempending on the input. 
    //either it gives all the results matching a sertain word on aither post_name or tagname
    //or it gets a limited part of that same result

    public function searchByTagsPostAndContent (string $search, int $page = null, int $pageLength = null) 
    {
        
        if (isset($page)) {
            
            $start = ($page -1) * $pageLength;

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id
            GROUP BY bi.id 
            HAVING bi.post_name LIKE :post_name OR bc.content LIKE :content OR tags LIKE :tagname 
            ORDER BY bi.post_creation_time DESC 
            LIMIT :start, :length';
            
            $statement = $this->db->prepare($query);

            $statement->bindValue("start", $start, PDO::PARAM_INT);
            $statement->bindValue("length", $pageLength, PDO::PARAM_INT);

        } else {

            $query = 'SELECT bi.*, bc.content, u.username, GROUP_CONCAT(t.tagname SEPARATOR " ") AS tags FROM blogposts_info bi 
            LEFT JOIN blogposts_content bc ON bc.id = bi.id 
            LEFT JOIN users u ON u.id = bi.user_id 
            LEFT JOIN post_tag_correspondens ptc ON ptc.post_id = bi.id 
            LEFT JOIN tags t ON t.id = ptc.tag_id
            GROUP BY bi.id 
            HAVING bi.post_name LIKE :post_name OR bc.content LIKE :content OR tags LIKE :tagname 
            ORDER BY bi.post_creation_time DESC';

            $statement = $this->db->prepare($query);
        }

        $statement->bindValue("post_name", "%$search%", PDO::PARAM_STR);
        $statement->bindValue("content", "%$search%", PDO::PARAM_STR);
        $statement->bindValue("tagname", "%$search%", PDO::PARAM_INT);

        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
        
        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::BLOGPOSTCLASSNAME);

        
        return $result;
    } // function that does on of two things dempending on the input. 
    //either it gives all the results matching a sertain word on aither post_name, content or tagname
    //or it gets a limited part of that same result

    public function deleteComentsFromBlogpost(int $id)
    {
        $query = 'DELETE FROM blogposts_comments WHERE post_id =:id';

        $statement = $this->db->prepare($query);
        $statement->bindValue("id", $id);
        
        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }
    }//functions responsible for handeling deltion of all the comments realtaed to a sertain blogpost

    public function deleteBlogpostContent(int $id)
    {

        $query = 'DELETE FROM blogposts_content WHERE id =:id';
        $statement = $this->db->prepare($query);
        $statement->bindValue("id", $id);
        
        if(!$statement->execute()) {
            throw new Exception($statement->errorinfo()[2]);
        }
    } //functions responsible for handeling deltion from the blogpost_content table

    public function deleteBlopostInfo(int $id)
    {
        $query = 'DELETE FROM blogposts_info WHERE id =:id';
        $statement = $this->db->prepare($query);
        $statement->bindValue("id", $id);
     
        if(!$statement->execute()) {     
            throw new Exception($statement->errorinfo()[2]);
        }           
    } //functions responsible for handeling deltion from the blogpost_info table

    public function deletePostFromDb(int $id) 
    {
        $this->db->beginTransaction();
        
        try {

            $this->deleteComentsFromBlogpost($id);
            $this->deleteBlogpostContent($id);
            $this->deletePostTagCorrespondens($id);
            $this->deleteBlopostInfo($id);
            
            $this->db->commit();

        } catch (Exception $e) {

            $this->db->rollBack();
            throw $e;
        }
    } // deletes a blogpost from the database

    public function insertCommentToDb(int $userId, int $blogpostId, string $content)
    {
        $query = 'INSERT INTO blogposts_comments(post_id, content, user_id, post_creation_time) VALUES (:post_id, :content, :user_id, NOW())';
        
        $statement = $this->db->prepare($query);

        $statement->bindValue('post_id', $blogpostId);
        $statement->bindValue('content', $content);
        $statement->bindValue('user_id', $userId);
       
        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }       

    } // inserts a new comment to the database

    public function getComments(int $id)
    {
        $query ='SELECT bc.* , u.username FROM blogposts_comments bc 
        LEFT JOIN users u ON u.id = bc.user_id 
        WHERE bc.post_id = :post_id';

        $statement = $this->db->prepare($query);

        $statement->bindValue('post_id', $id);


        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        } 

        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::COMMENTCLASSNAME);

        return $result;

    } // gets all comments related to a spesific blogpost

    public function deleteCommentFromDb(int $id)
    {
        $query = 'DELETE FROM blogposts_comments WHERE id =:id';
        $statement = $this->db->prepare($query);
        $statement->bindValue("id", $id);
        
        if(!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);
        }

        $statement->execute();
    } // removes a comment with a spesific id

    public function getComment(int $id)
    {
        $query ='SELECT bc.* , u.username FROM blogposts_comments bc 
        LEFT JOIN users u ON u.id = bc.user_id 
        WHERE bc.id = :id';

        $statement = $this->db->prepare($query);

        $statement->bindValue('id', $id);

        if(!$statement->execute()) {   

            throw new Exception($statement->errorinfo()[2]);
        } 

        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::COMMENTCLASSNAME);

        return $result;

    } //selects a spisific comment from a givven id

    public function editComment(int $id, string $content) 
    {
        $query = 'UPDATE blogposts_comments SET content =:content, post_edit_time = NOW() WHERE id =:id';

        $statement = $this->db->prepare($query);

        $statement->bindValue('id', $id);
        $statement->bindValue('content', $content);

        if (!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);;
        }
        
        $statement->execute();
    
    } //updated a spisific comment with the provided content

    public function topTags() : array
    {
        $query = 'SELECT DISTINCT t.*, COUNT(ptc.tag_id) AS count FROM tags t 
        LEFT JOIN post_tag_correspondens ptc ON ptc.tag_id = t.id 
        GROUP BY t.tagname ORDER BY count DESC LIMIT 3';

        $statement = $this->db->prepare($query);
    
        if (!$statement->execute()) {

            throw new Exception($statement->errorinfo()[2]);;
            
        }
       
        $statement->execute();

        $result = $statement->fetchAll();
        
        return $result;
        
    } // query that pulls out the 3 most used tags from the database
}
?>