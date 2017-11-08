<section>  
    <article id="create-comment-form"> 
        <form action="/CommentToDatabase" method="post">
            <input id="content" name ="content" type="text"  required>
            <button type="submit" value=<?php echo $blogpostId ?>>Skapa kommentar</button>
        </form>  
    </article>
</section>