<?php  foreach($comments as $comment): ?>
<section>  
    <article id="update-comment-form"> 
        <form action="/editComment/<?php echo $comment->getId()?>" method="post">
            <input id="content" name ="content" type="text" value=<?php echo "'" . $comment->getContent() . "'"?> required>
            <button name="comment_id" type="submit" value=<?php echo $comment->getId()?>>skicka in ändringarna!</button>
        </form>  
    </article>
</section>
<?php endforeach ?>