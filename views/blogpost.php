<section>
<?php  foreach($blogposts as $blogpost): ?>
            <?php if(isset($userId) && !empty($userId) && $userId === $blogpost->getUserId()): ?>
            <form action=<?php echo "/start/logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
                <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >editera inlägget</button>
            </form>
            <form action="/deletePostFromDB" method="get">
                <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >tabort inlägget</button>
            </form>
            <?php endif ?>
        <article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo 'Tags: ' . $tags?></p>
            <p><?php echo $blogpost->getContent() ?></p>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostCreationTime() ?></p>
        </article>
    <?php endforeach ?>
</section>