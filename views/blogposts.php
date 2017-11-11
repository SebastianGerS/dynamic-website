<section>
    <?php if(isset($_COOKIE[user])): ?>
        <form action="/start/logedin/createBlogposts" method="get">
            <button> Create new blogpost</button>
        </form>
        <form action="/start/logedin/my-blogposts" method="get">
            <button> View my blogposts</button>
        </form>
    <?php endif ?>
    <?php if($morePages): ?>
        <form action="<?php echo $nextPage ?>" method="get">
            <button> Nästa sida</button>
        </form>
    <?php endif ?>
    <?php if($page !==1): ?>
        <form action=<?php echo $previusPage ?> method="get">
            <button> Föregående sida</button>
        </form>
    <?php endif ?>
</section>
<?php if(isset($errorMessage)): ?> 
    <section id="errorMessage">
        <h1><?php echo $errorMessage ?></h1> 
    </section>
<?php endif ?>
<section>
    <?php  foreach($blogposts as $blogpost): ?>
        <a href=<?php echo '/start/blogpost/' . $blogpost->getId()?>><article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostCreationTime() ?></p>
            <p><?php echo substr($blogpost->getContent(), 0, 25) ?></p>
        </article></a>
        <?php if(isset($userId) && !empty($userId) && $userId === $blogpost->getUserId()|| $_COOKIE[userType] === "admin" ): ?>
        <form action=<?php echo "logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
            <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >editera inlägget</button>
        </form>
        <form action="/deletePostFromDB" method="get">
                <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >tabort inlägget</button>
        </form>
        <?php endif ?>
    <?php endforeach ?>
</section>