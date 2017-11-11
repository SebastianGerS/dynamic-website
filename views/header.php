<header>
    <div id="logo">
    <img src="#" alt="logo">
    </div>
    <div id="center-header">
    <h1>Välkommen till bloggen!</h1>
    <form id=search-form action="/blogposts/search" method="get">
        <div>
            <input type=text placeholder="type your search"  name="tagname">
            <button type="submit"> Sök!</button>
        </div>
        <div id="hashtags">
            <button>#programmering</button>
            <button>#musik</button>
            <button>#Tv-serier</button>    
        </div>
    </form>
    </div>
    <?php if(!isset($_COOKIE['user'])): ?>
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
<body>