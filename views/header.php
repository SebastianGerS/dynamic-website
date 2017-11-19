<nav class="navbar navbar-dark bg-primary col-12">
    <div class ="row col-12 align-items-start justify-content-center mx-0">
        <div class"col mx-4">
            <form class ="row col-12 align-items-start justify-content-center my-3 pt-2" action="/start/blogposts/search/1" method="get">
                <div class="input-group">
                    <input class="form-control" id="searchfield" type=text placeholder="type your search"  name="search">
                    <button class="btn btn-primary bg-success mr-2" type="submit"> Sök!</button>
                    <div class="btn-group text-light" data-toggle="buttons">
                        <lable class="btn btn-success border active checkbox" >
                        <input type="checkbox" name="tags" value="true" checked>Tagar
                        </lable>
                        <lable class="btn btn-warning border mx-1 checkbox"  >
                        <input type="checkbox" name="post_name" value="true">Titel
                        </lable>
                        <lable class="btn btn-warning border checkbox" >
                        <input type="checkbox" name="content" value="true"> Innehåll
                        </lable>
                    </div>
                </div>
            </form>
            <div class="alert alert-info py-0">
                <h6 class="text-dark text-center mt-1">Klicka på knapparna för att välja vad du vill söka på</h6>
            </div>
        </div>
        <div class="col offset-1 ml-4">
            <h1 class="text-light pt-5">Välkommen!</h1>
        </div>
        <div class ="col-3 mt-2 ml-1">
                <h4 class="text-light mb-3 pt-3">Populära taggar</h4>
                <div id="hashtags">
                    <button class="btn btn-warning"><?php echo $toptag1 ?></button>
                    <button class="btn btn-warning"><?php echo $toptag2 ?></button>
                    <button class="btn btn-warning"><?php echo $toptag3 ?></button>   
                </div> 
            </div>
            <?php if(!isset($user)): ?>
                <div class="col ml-0 pt-1">
                    <form  action="/login" method="post">
                        <div class="input-group pb-1 mb-1">
                            <input class="form-control" type="text" name="username" placeholder="Användarnamn" autocomplete="off">
                        </div>
                        <div class="input-group pb-1 mb-1">
                            <input  class="form-control " type="text" name="password" placeholder="Lösenord" autocomplete="off">
                        </div>
                        <button class="btn btn-primary bg-success ml-5 mb-2" type="submit">Logga in</button>
                    </form>
                    <div>
                        <a class="text-light ml-3" href="/start/createUser">skapa en användare!</a>
                    </div>
                <?php else: ?>
                    <div class="col ml-3 pt-1">
                        <form class="pt-5 ml-2" action="/logout" method="post">
                            <button class="btn btn-warning mb-3" type="submit">Logga ut</button>
                        </form>
            <?php endif ?>
        </div>
    </div>
    <div class="row col-12 mx-0 bg-info align-items-end justify-content-center">
         <div>
            <ul class="nav col-12 my-1"  action="/start/logedin/createBlogposts" method="get">
                 <li class="nav-item mr-2">
                    <a class="nav-link bg-primary text-light" href="/start/blogposts"> View All blogposts</a>
                </li>
                <?php if(isset($user)): ?>
                    <li class="nav-item mx-2">
                        <a class="nav-link bg-primary text-light" href="/start/logedin/createBlogposts"> Create new blogpost</a>
                    </li>
                    <li class="nav-item ml-2">
                        <a class="nav-link bg-primary text-light" href="/start/logedin/my-blogposts"> View my blogposts</a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>   
    <?php if(isset($errorMessage)): ?> 
        <div class="row col-12 align-items-end alert alert-warning">
            <section class="col-12" id="errorMessage">
                <h5><?php echo $errorMessage ?></h5> 
            </section>
        </div>        
    <?php endif ?>
</nav>
<body class="bg-success">
    <div class="col-10 bg-light container offset-1">