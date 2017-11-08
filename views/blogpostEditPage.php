<?php  foreach($blogposts as $blogpost): ?>
<section>  
    <article id="update-blogpost-form"> 
        <form action="/edit/<?php echo $blogpost->getId()?>" method="post">
            <input name="post_name" type="text" value=<?php echo  "'" . $blogpost->getPostName() . "'"?> required>
            <input name="tagname" type="text" placeholder="Tags" value=<?php echo "'" . $tags . "'"?> required>
            <input id="content" name ="content" type="text" value=<?php echo "'" . $blogpost->getContent() . "'"?> required>
            <button name="blogpost_id" type="submit" value=<?php echo $blogpost->getId()?>>skicka in ändringarna!</button>
        </form>  
    </article>
</section>
<?php endforeach ?>