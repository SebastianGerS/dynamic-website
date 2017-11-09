<section>
    <?php if(isset($_COOKIE[user])): ?>
        <form action="/start/logedin/createBlogposts"? method="post">
            <button> Create new blogpost</button>
        </form>
    <?php endif ?>
    <?php if($morePages): ?>
        <form action="/start/logedin/<?php echo $page+1 ?>" method="post">
            <button> Nästa sida</button>
        </form>
    <?php endif ?>
    <?php if($page !==1): ?>
        <form action="/start/logedin/<?php echo $page-1 ?>" method="post">
            <button> Föregående sida</button>
        </form>
    <?php endif ?>
</section>

<section><?php if(isset($errorMessage)): ?> 
    <h2 id="errorMessage"><?php echo $errorMessage ?></h2>
    <?php endif ?>
</section>

<section>
<?php  foreach($blogposts as $blogpost): ?>
        <a href=<?php echo '/blogpost/' . $blogpost->getId()?>><article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostCreationTime() ?></p>
            <p><?php echo substr($blogpost->getContent(), 0, 25) ?></p>
        </article></a>
        <?php if(isset($userId) && !empty($userId) && $userId === $blogpost->getUserId()): ?>
        <form action=<?php echo "logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
            <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >editera inlägget</button>
        </form>
        <form action="/deletePostFromDB" method="get">
                <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >tabort inlägget</button>
        </form>
        <?php endif ?>
    <?php endforeach ?>
</section>