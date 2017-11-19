<section>
<?php //var_dump($previousPage);  ?>
    <?php if(isset($rootPage)) { 
        echo
        "<form action=$rootPage method='post'>
            <button  class='btn btn-success my-2' > Tillbaka</button>
        </form>";
    }else if (isset($back) && preg_match("~(edit|create)~",$back) === 0){
        echo
        "<form action=$back method='post'>
            <button  class='btn btn-success my-2' > Tillbaka</button>
        </form>";
    } else if (isset($user)){
        echo
        "<form action='/start/logedin/blogposts' method='post'>
            <button  class='btn btn-success my-2' > Tillbaka</button>
        </form>";
    } else {
        echo
        "<form action='/start/blogposts' method='post'>
            <button  class='btn btn-success my-2' > Tillbaka</button>
        </form>";
    }?>
    <div class="col-12 card mb-1">
        <div class="card-body col-12">
            <div class="row justify-content-center">
                <article class="col-11">
                    <div class="row">
                        <p class="card-text mr-3"><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
                        <p class="card-text mr-3"><?php echo 'Datum: ' . $blogpost->getPostCreationTime() ?></p>
                        <?php if ($blogpost->issetPostEditTime() === true): ?>
                            <p class="card-text mr-3"><?php echo 'Senast editerad: ' . $blogpost->getPostEditTime() ?></p>
                        <?php endif ?>
                        <p class="card-text"><?php echo 'Tags: ' . $blogpost->getTags()?></p>
                    </div>
                    <div class="row justify-content-center">
                        <h1 class="card-title py-2"><?php echo $blogpost->getPostName() ?></h1>
                    </div>
                        <p class="card-text py-3"><?php echo $blogpost->getContent() ?></p>
                </article>
            </div>
            <div class="row col-12 justify-content-center">
                <?php if(isset($user)): ?>
                        <form action="/start/logedin/createComment" method="post">
                            <input name="rootPage" type="text" value=<?php  if(isset($rootPage)){echo $rootPage;}else {echo $back;}?> required style="display: none;">
                            <button class="btn btn-success mr-5" name="blogpost_id" value=<?php echo $blogpost->getId()?>>Kommentera</button>
                        </form>
                <?php endif ?>
                <?php if(isset($user) && $user->id == $blogpost->getUserId()|| $user->type === "admin" ): ?>
                    <form action=<?php echo "/start/logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
                        <input name="rootPage" type="text" value=<?php  if(isset($rootPage)){echo $rootPage;}else {echo $back;}?> required style="display: none;">
                        <button class="btn btn-info mx-5" name="blogpost_id" value=<?php echo $blogpost->getId()?> >Editera inlägget</button>
                    </form>
                    <form action="/deletePostFromDB" method="get">
                        <button class="btn btn-danger ml-5" name="blogpost_id" value=<?php echo $blogpost->getId()?> >Tabort inlägget</button>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </div>
    <?php if(!empty($comments)): ?>   
        <?php foreach($comments as $comment): ?>
            <div class="col-10 offset-1 bg-info mb-1 card">
                <div class="card-body col-12">
                    <div class="row ">
                        <article class="col-12 my-3">
                            <div class="row justify-content-center">
                                <p class="card-text text-light mr-5"><?php echo 'Kommentar av: ' . $comment->getUsername() ?></p>
                                <p class="card-text text-light mr-5"><?php echo 'Datum: ' . $comment->getPostCreationTime() ?></p>
                                <?php if ($comment->issetPostEditTime()): ?>
                                    <p class="card-text text-light"><?php echo 'Senast editerad: ' . $comment->getPostEditTime()?></p>
                                <?php endif ?>
                            </div>
                            <div class="row justify-content-center">
                                <p class="card-text text-light"><?php echo $comment->getContent() ?></p>
                            </div>
                        </article>
                    </div>
                    <?php if(isset($user) && $user->id == $comment->getUserId() || $user->type === "admin" ): ?>
                        <div class="row justify-content-center">
                            <form action=<?php echo "/start/logedin/editComment/" . $comment->getId() ?> method="post">
                                <input name="rootPage" type="text" value=<?php  if(isset($rootPage)){echo $rootPage;}else {echo $back;}?> required style="display: none;">
                                <button  class="btn btn-warning mr-5" name="comment_id" value=<?php echo $comment->getId()?> >Editera</button>
                            </form>
                            <form action="/deleteCommentFromDB" method="get">
                                <input name="rootPage" type="text" value=<?php  if(isset($rootPage)){echo $rootPage;}else {echo $back;}?> required style="display: none;">
                                <input name="blogpost_id" type="text" value=<?php  echo $blogpost->getId() ?> required style="display: none;">
                                <button  class="btn btn-danger ml-5" name="comment_id" value=<?php echo $comment->getId()?> >Tabort</button>
                            </form>
                        </div>
                    <?php endif ?> 
                </div>
            </div>
        <?php endforeach ?>
    <?php endif ?>    
</section>