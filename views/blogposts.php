</section><?php if(isset($userId) && $userId !== null) {
    echo '<a href="/start/logedin/createBlogposts"><button>Create new blogpost</button></a>';
    };?>

<section>
<section>
<?php  foreach($blogposts as $blogpost): ?>
        <a href=<?php echo '/blogpost/' . $blogpost->getId()?>><article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostTime() ?></p>
            <p><?php echo substr($blogpost->getContent(), 0, 25) ?></p>
        </article></a>
        <?php if(isset($userId) && $userId !== null && $userId == $blogpost->getUserId()): ?>
            <a href=<?php echo "logedin/editBlogpost/" . $blogpost->getId() ?> >editera inlägget</a>
        <?php endif ?>
    <?php endforeach ?>
</section>