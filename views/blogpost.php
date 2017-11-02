<section>
<?php  foreach($blogposts as $blogpost): ?>
        <article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo $blogpost->getContent() ?></p>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostTime() ?></p>
        </article>
    <?php endforeach ?>
</section>