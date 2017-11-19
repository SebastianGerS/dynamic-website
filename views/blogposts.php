<section class="py-3">
    <div class="col-12 row">
        <?php if(isset($page) && $page !==1): ?>
            <form class ="col-1" action=<?php echo $previusPage ?> method="get">
                <button class="btn btn-warning my-3"> Föregående sida</button>
            </form>
        <?php endif ?>
        <?php if($morePages): ?>
            <form class ="col-1 offset-10<?php if($page ==1) {echo ' offset-11';}?>" action="<?php echo $nextPage ?>" method="get">
                <button class="btn btn-success my-3" name="same_search" value ="true"> Nästa sida</button>
            </form>
        <?php endif ?>
    </div>
    <?php  foreach($blogposts as $blogpost): ?>
    <?php //var_dump($blogposts); die; ?>
        <div class="col-12 card my-1">
            <div class="card-body">
                <div class="row col-12">
                    <a class="text-dark"href=<?php echo $path . "/" . $blogpost->getId()?>><article>
                        <h1 class="card-title"><?php echo $blogpost->getPostName() ?></h1>
                        <p class="card-text"><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
                        <p class="card-text"><?php echo 'Datum: ' . $blogpost->getPostCreationTime() ?></p>
                        <?php if ($blogpost->issetPostEditTime()): ?>
                            <p class="card-text mr-5"><?php echo 'Senast editerad: ' . $blogpost->getPostEditTime() ?></p>
                        <?php endif ?>
                        <p class="card-text"><?php echo 'Tags: ' . $blogpost->getTags()?></p>
                        <p class="card-text"><?php echo 'Innehåll: ' . substr($blogpost->getContent(), 0, 25) . ' ...' ?></p>
                    </article></a>
                </div>
            <?php if(isset($user) && $user->id == $blogpost->getUserId()|| $user->type == "admin" ): ?>
                <div class="col-12 row justify-content-center">
                    <form action=<?php echo "/start/logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
                        <button class="btn btn-info mr-5" name="blogpost_id" value=<?php echo $blogpost->getId()?>>editera</button>
                    </form>
                    <form action="/deletePostFromDB" method="get">
                            <button class="btn btn-danger ml-5" name="blogpost_id" value=<?php echo $blogpost->getId()?> >tabort</button>
                    </form>
                </div>
            <?php endif ?>
            </div>
        </div>
       
    <?php endforeach ?>
</section>