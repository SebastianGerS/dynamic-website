<section>
    <form action=<?php echo "/start/logedin/blogposts"?> method="post">
        <button  class="btn btn-success my-2" > Tillbaka</button>
    </form>
    <div class="col-12 card mb-1">
        <div class="card-body col-12">
            <div class="row justify-content-center">
                <article class="col-11">
                    <div class="row">
                        <p class="card-text mr-5"><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
                        <p class="card-text mx-5"><?php echo 'Datum: ' . $blogpost->getPostCreationTime() ?></p>
                        <p class="card-text ml-5"><?php echo 'Tags: ' . $tags?></p>
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
                            <button class="btn btn-success mr-5" name="blogpost_id" value=<?php echo $blogpost->getId()?>>Kommentera</button>
                        </form>
                <?php endif ?>
                <?php if(isset($user) && $user->id == $blogpost->getUserId()|| $user->type === "admin" ): ?>
                    <form action=<?php echo "/start/logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
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
                                <p class="card-text text-light ml-5"><?php echo 'Datum: ' . $comment->getPostCreationTime() ?></p>
                            </div>
                            <div class="row justify-content-center">
                                <p class="card-text text-light"><?php echo $comment->getContent() ?></p>
                            </div>
                        </article>
                    </div>
                    <?php if(isset($user) && $user->id == $comment->getUserId() || $user->type === "admin" ): ?>
                        <div class="row justify-content-center">
                            <form action=<?php echo "/start/logedin/editComment/" . $comment->getId() ?> method="post">
                                <button  class="btn btn-warning mr-5" name="comment_id" value=<?php echo $comment->getId()?> >Editera</button>
                            </form>
                            <form action="/deleteCommentFromDB" method="get">
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