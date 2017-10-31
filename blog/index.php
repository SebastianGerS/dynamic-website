<?php
use Blog\Utils\Connection;
use Blog\Models\BlogpostModel;
use BLog\Models\UserModel;

function autoloader($classname)
{
    $lastSlash = strpos($classname,'\\') +1;
    $classname = substr($classname, $lastSlash);
    $directory = str_replace('\\', '/', $classname);
    $filename = __DIR__ . '/src/' . $directory . '.php';
    require_once($filename);
}

spl_autoload_register('autoloader');
//require_once( __DIR__ . '/src/Functions/userFunctions.php');
//require_once( __DIR__ . '/src/Functions/blogpostFunctions.php');

/*try {
addUser("sven", "svensson", "exempel@exempel.exempel", "Sven", "root", "user");
} catch (Exception $e) {
    echo "error adding user" . $e->getMessage();
}*/




$userModel = new UserModel();

/*try {
    $blogpostmaker->insertBlogPostToDb(2, "Mitt första blogg inlägg", "Hej hej det här är mitt första blog inlägg,
 jag hoppas att det ska fungera och att mina tabeller kommer att uppdateras som de ska. Tack för mej. Hej Hej.", ["#hejhej", "#t", "#ber"]);
} catch (Exception $e) {
    echo "error changing type" . $e->getMessage();
}*/
try {
    $userModel->addUser("sven", "svensson", "exeml@exempel.exempel", "Sen", "root");
    } catch (Exception $e) {
        echo "error adding user" . $e->getMessage();
    }
try {
    $userModel->editUserType("Sven", "user");
    } catch (Exception $e) {
        echo "error changing type" . $e->getMessage();
    }
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