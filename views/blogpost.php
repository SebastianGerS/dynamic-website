<section>
<?php  foreach($blogposts as $blogpost): ?>
            <?php if(isset($userId) && $userId !== null && $userId == $blogpost->getUserId()): ?>
            <form action=<?php echo "/start/logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
                <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >editera inlägget</button>
                <form>
            <?php endif ?>
        <article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo $blogpost->getContent() ?></p>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostTime() ?></p>
        </article>
    <?php endforeach ?>
</section>