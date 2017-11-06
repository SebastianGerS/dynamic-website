<?php
use Blog\Core\Router;
use Blog\Core\Request;


function autoloader($classname)
{
    $lastSlash = strpos($classname,'\\') +1;
    $classname = substr($classname, $lastSlash);
    $directory = str_replace('\\', '/', $classname);
    $filename = __DIR__ . '/src/' . $directory . '.php';
    require_once($filename);
}

spl_autoload_register('autoloader');


$router = new Router();

$response = $router->route(new Request());
include_once("templates/head.html");
include_once("views/header.php");

echo $response;
include_once("templates/footer.html");



//require_once( __DIR__ . '/src/Functions/userFunctions.php');
//require_once( __DIR__ . '/src/Functions/blogpostFunctions.php');

/*try {
addUser("sven", "svensson", "exempel@exempel.exempel", "Sven", "root", "user");
} catch (Exception $e) {
    echo "error adding user" . $e->getMessage();
}*/




//$userModel = new UserModel();

/*try {
    $blogpostmaker->insertBlogPostToDb(2, "Mitt första blogg inlägg", "Hej hej det här är mitt första blog inlägg,
 jag hoppas att det ska fungera och att mina tabeller kommer att uppdateras som de ska. Tack för mej. Hej Hej.", ["#hejhej", "#t", "#ber"]);
} catch (Exception $e) {
    echo "error changing type" . $e->getMessage();
}*/
/*try {
    $userModel->addUser("sven", "svensson", "exeml@exempel.exempel", "Sen", "root");
    } catch (Exception $e) {
        echo "error adding user" . $e->getMessage();
    }
try {
    $userModel->editUserType("Sven", "user");
    } catch (Exception $e) {
        echo "error changing type" . $e->getMessage();
    }*/
?>

