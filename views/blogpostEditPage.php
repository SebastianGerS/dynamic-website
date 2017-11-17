<div class="form-group py-3"> 
    <article > 
        <form action="/edit/<?php echo $blogpost->getId()?>" method="post">
            <input class="form-control my-3" name="post_name" type="text" value=<?php echo  "'" . $blogpost->getPostName() . "'"?> required autocomplete="off">
            <input class="form-control my-3" name="tagname" type="text" placeholder="Tags" value=<?php echo "'" . $blogpost->getTags() . "'"?> required autocomplete="off">
            <textarea class="form-control my-3" name ="content" type="text" required autocomplete="off"><?php echo $blogpost->getContent()?></textarea>
            <button  class="btn btn-success my-3" name="blogpost_id" type="submit" value=<?php echo $blogpost->getId()?>>skicka in ändringarna!</button>
        </form>  
    </article>
</div>