<div class="form-group py-3">
    <article class="form-group py-3"> 
        <form action="/editComment/<?php echo $comment->getId()?>" method="post">
            <textarea class="form-control my-3" name ="content" type="text" required><?php echo $comment->getContent()?></textarea>
            <input  name="blogpost_id" type="text" value=<?php  echo $comment->getPostId() ?> required style="display: none;">
            <button class="btn btn-success my-3" name="comment_id" type="submit" value=<?php echo $comment->getId()?>>skicka in ändringarna!</button>
        </form>  
    </article>
</div>