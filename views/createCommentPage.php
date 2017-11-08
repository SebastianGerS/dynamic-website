<section>  
    <article id="create-comment-form"> 
        <form action="/CommentToDatabase" method="post">
            <input id="content" name ="content" type="text" required>
            <input name="blogpost_id" type="text" value=<?php  echo $blogpostId ?> required style="display: none;">
            <button type="submit">Skapa blogpost</button>
        </form>  
    </article>
</section>