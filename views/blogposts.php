<section>
<?php  foreach($blogposts as $blogpost): ?>
        <article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo $blogpost->getUserId() ?></p>
            <p><?php echo $blogpost->getContent() ?></p>
        </article>
    <?php endforeach ?>
</section>