<div class="form-group py-3">
    <article id="create-comment-form"> 
        <form action="/CommentToDatabase" method="post">
            <textarea class="form-control my-3" id="content" name ="content" type="text" required></textarea>
            <input class="form-control my-3"name="blogpost_id" type="text" value=<?php  echo $blogpostId ?> required style="display: none;">
            <button class="btn btn-success my3"type="submit">Skapa kommenter</button>
        </form>  
    </article>
</div>