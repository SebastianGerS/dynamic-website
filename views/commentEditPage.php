<section>  
    <article id="update-comment-form"> 
        <form action="/editComment/<?php echo $comment->getId()?>" method="post">
            <input id="content" name ="content" type="text" value=<?php echo "'" . $comment->getContent() . "'"?> required>
            <input name="blogpost_id" type="text" value=<?php  echo $comment->getPostId() ?> required style="display: none;">
            <button name="comment_id" type="submit" value=<?php echo $comment->getId()?>>skicka in ändringarna!</button>
        </form>  
    </article>
</section>