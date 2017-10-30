<?php
use Blog\Utils\Connection;

function autoloader($classname)
{
    $lastSlash = strpos($classname,'\\') +1;
    $classname = substr($classname, $lastSlash);
    $directory = str_replace('\\', '/', $classname);
    $filename = __DIR__ . '/src/' . $directory . '.php';
    require_once($filename);
}

spl_autoload_register('autoloader');
//require_once( __DIR__ . '/src/Functions/Userfunc.php');

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

/*try {
addUser("sven", "svensson", "exempel@exempel.exempel", "Sven", "root", "user");
} catch (Exception $e) {
    echo "error adding user" . $e->getMessage();
}*/

function editUserType(string $username, string $type) 
{
    $db = Connection::getInstance();
    $db->handler->beginTransaction();
    try {
    $query = 'UPDATE users SET type = :type WHERE username = :username';
    $statement = $db->handler->prepare($query);
    $statement->bindValue("username", $username);
    $statement->bindValue("type", $type);
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

/*try {
editUserType("Sven", "user");
} catch (Exception $e) {
    echo "error changing type" . $e->getMessage();
}*/


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
        }//detta fungerar


        $postId = $db->handler->lastInsertId("blogposts_info");
        //settype($postId,"integer");
        //echo $postId;
        $query = 'INSERT INTO blogposts_content(id, content) VALUES (:id , :content)';
        $statement = $db->handler->prepare($query);
        $statement->bindValue("id", $postId);
        $statement->bindValue("content", $content);
        if(!$statement->execute()) 
        {
            throw new Exception($statement->errorinfo()[2]);
        } // detta fungerar

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
                echo "det är här det blir fel... men varför?";
                echo $statement->errorCode();
                throw new Exception($statement->errorInfo()[2]);
                
            }
        }
        $db->handler->commit();
    } catch (Exception $e) {
        $db->handler->rollBack();
        throw $e;
    }

}


/*try {
newBlogPost(1, "Mitt första blogg inlägg", "Hej hej det här är mitt första blog inlägg,
 jag hoppas att det ska fungera och att mina tabeller kommer att uppdateras som de ska. Tack för mej. Hej Hej.", ["#i3", "#t", "#ber"]);
} catch (Exception $e) {
    echo "error changing type" . $e->getMessage();
}*/



/*$db = Connection::getInstance();
$tag ="green";
$query = 'SELECT id FROM tags WHERE tagname =:tagname';
$statement = $db->handler->prepare($query);
$statement->bindValue("tagname", $tag);
if(!$statement->execute()) 
{
    throw new Exception($statement->errorinfo()[2]);
}
$statement->execute();
$tagId = $statement->fetch(PDO::FETCH_NUM)[0];

echo $tagId;
$tag2 ="blue";
$query = 'SELECT id FROM tags WHERE tagname=:tagname';
$statement = $db->handler->prepare($query);
$statement->bindValue("tagname", $tag2);
$statement->execute();


$id = $statement->fetch(PDO::FETCH_NUM)[0];
echo $id;


/*$query = 'SELECT tagname FROM tags';
$statement = $db->handler->prepare($query);
$statement->execute();
$curentTags = $statement->fetchAll(PDO::FETCH_ASSOC);
$newtag = yellow;
foreach ($curentTags as $tag) {
    print_r($tag);
    if (!array_search($newtag, $curentTags)) {
    echo "its not in here here";
    array_push($curentTags, $newtag);
    } else {
    echo "it's here";
    print_r($curentTags); 
    }
}*/

//$statement->execute();
//var_dump($statement);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel ="stylesheet" type="text/css" href="css/main.css">
        <title>05-dynamisk-webbplats-php-SebastianGerS</title>
    </head>
    <header>
        <div id="logo">
        <img src="#" alt="logo">
        </div>
        <div id="center-header">
        <h1>Välkommen till bloggen!</h1>
        <form id=search-form>
            <input type=text placeholder="type your search">
            <div id="hashtags">
            <button>#programmering</button>
            <button>#musik</button>
            <button>#Tv-serier</button>
            </div>
        </form>
        </div>
        <div id="login-form">
            <form>
                <label>Logga in</label>
                <input type="text" placeholder="Användarnamn">
                <input type="text" placeholder="Lösenord">
            </form>
        <div>
    </header>
    <body>
        <section>      
            <article>
                <h1>Lorem ipsum dolor</h1>
                <p>Lorem ipsum dolor sit amet, an sea quot timeam. Habemus invenire per no. Alterum oporteat repudiare quo eu. Mea graeci aeterno et. Id facer augue quo, pri ei everti meliore.
                        Ea discere euismod elaboraret qui, sed omnis semper qualisque ad, cum maiestatis definiebas ea. Ut verear petentium mel. Persius epicurei expetenda ei eum, veri quaestio suavitate per te, sit ut iudicabit percipitur. Quod constituam interpretaris ei mei, sed verterem gubergren consequuntur te, tota constituto te qui.</p>
            </article>
            <nav>
                <button>skapa inlägg</button>
                <button>Editera inlägg</button>
                <button>tabort inlägg</button>
            </nav>
        </section>
        <section>   
            <article>
                <h1>Lorem ipsum dolor</h1>
                <p>Lorem ipsum dolor sit amet, an sea quot timeam. Habemus invenire per no. Alterum oporteat repudiare quo eu. Mea graeci aeterno et. Id facer augue quo, pri ei everti meliore.
                    Ea discere euismod elaboraret qui, sed omnis semper qualisque ad, cum maiestatis definiebas ea. Ut verear petentium mel. Persius epicurei expetenda ei eum, veri quaestio suavitate per te, sit ut iudicabit percipitur. Quod constituam interpretaris ei mei, sed verterem gubergren consequuntur te, tota constituto te qui.</p>
            </article>
            <nav>
                <button>skapa inlägg</button>
                <button>Editera inlägg</button>
                <button>tabort inlägg</button>
            </nav>
        </section>
        <section>
            <article>
                <h1>Lorem ipsum dolor</h1>
                <p>Lorem ipsum dolor sit amet, an sea quot timeam. Habemus invenire per no. Alterum oporteat repudiare quo eu. Mea graeci aeterno et. Id facer augue quo, pri ei everti meliore.
                    Ea discere euismod elaboraret qui, sed omnis semper qualisque ad, cum maiestatis definiebas ea. Ut verear petentium mel. Persius epicurei expetenda ei eum, veri quaestio suavitate per te, sit ut iudicabit percipitur. Quod constituam interpretaris ei mei, sed verterem gubergren consequuntur te, tota constituto te qui.</p>
            </article>
            <nav>
                <button>skapa inlägg</button>
                <button>Editera inlägg</button>
                <button>tabort inlägg</button>
            </nav>
        </section>
    </body>
    <footer>
        <h1>Lorem ipsum dolor</h1>
        <p>Lorem ipsum dolor sit amet, an sea quot timeam. Habemus invenire per no. Alterum oporteat repudiare quo eu. Mea graeci aeterno et. Id facer augue quo, pri ei everti meliore.
            Ea discere euismod elaboraret qui, sed omnis semper qualisque ad, cum maiestatis definiebas ea. Ut verear petentium mel. Persius epicurei expetenda ei eum, veri quaestio suavitate per te, sit ut iudicabit percipitur. Quod constituam interpretaris ei mei, sed verterem gubergren consequuntur te, tota constituto te qui.</p>
    </footer>
</html>