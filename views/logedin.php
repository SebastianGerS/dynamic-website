<section> 

</section>
<button>Create new blogpost</button>
<section>
<?php  foreach($blogposts as $blogpost): ?>
        <a href=<?php echo '/blogpost/' . $blogpost->getId()?>><article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostTime() ?></p>
            <p><?php echo substr($blogpost->getContent(), 0, 25) ?></p>
        </article></a>
        <button>Edit Blogpost</button>
    <?php endforeach ?>
</section>