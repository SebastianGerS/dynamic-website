<header>
    <div id="logo">
    <img src="#" alt="logo">
    </div>
    <div id="center-header">
    <h1>Välkommen till bloggen!</h1>
    <form id=search-form action="/blogposts/search/1" method="get">
        <div>
            <input type=text placeholder="type your search"  name="search">
            <input type="checkbox" name="tags" value="true" checked>
            <input type="checkbox" name="post_name" value="true">
            <input type="checkbox" name="content" value="true">
            <button type="submit"> Sök!</button>
        </div>
        <div id="hashtags">
            <button><?php echo $toptag1 ?></button>
            <button><?php echo $toptag2 ?></button>
            <button><?php echo $toptag3 ?></button>    
        </div>
    </form>
    </div>
    <?php if(!isset($user)): ?>
    <div id="login-form">
        <a href="/start/createUser">skapa användare</a>
            <form action="/login" method="post">
                <input type="text" name="username" placeholder="Användarnamn">
                <input type="text" name="password" placeholder="Lösenord">
                <button type="submit">Logga in</button>
            </form>
        <?php else: ?>
            <form action="/logout" method="post">
                <button type="submit">Logga ut</button>
            </form>
        <?php endif ?>
    <div>
</header>
<nav> 
    <?php if(isset($user)): ?>
        <form action="/start/logedin/createBlogposts" method="get">
            <button> Create new blogpost</button>
        </form>
        <form action="/start/logedin/my-blogposts" method="get">
            <button> View my blogposts</button>
        </form>
    <?php endif ?>
</nav>
</section>
<?php if(isset($errorMessage)): ?> 
    <section id="errorMessage">
        <h1><?php echo $errorMessage ?></h1> 
    </section>
<?php endif ?>
<section>
<body>