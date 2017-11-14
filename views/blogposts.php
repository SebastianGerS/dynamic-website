<section>
    <?php if($morePages): ?>
        <form action="<?php echo $nextPage ?>" method="get">
            <button> Nästa sida</button>
        </form>
    <?php endif ?>
    <?php if(isset($page) && $page !==1): ?>
        <form action=<?php echo $previusPage ?> method="get">
            <button> Föregående sida</button>
        </form>
    <?php endif ?>
    <?php  foreach($blogposts as $blogpost): ?>
        <a href=<?php echo $path . "/" . $blogpost->getId()?>><article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostCreationTime() ?></p>
            <p><?php echo substr($blogpost->getContent(), 0, 25) ?></p>
        </article></a>
        <?php if(isset($user) && $user->id == $blogpost->getUserId()|| $user->type == "admin" ): ?>
        <form action=<?php echo "logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
            <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >editera inlägget</button>
        </form>
        <form action="/deletePostFromDB" method="get">
                <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >tabort inlägget</button>
        </form>
        <?php endif ?>
       
    <?php endforeach ?>
</section>