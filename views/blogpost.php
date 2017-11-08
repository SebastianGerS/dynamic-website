<section>
    <?php  foreach($blogposts as $blogpost): ?>
        <?php if(isset($userId) && !empty($userId) && $userId === $blogpost->getUserId()): ?>
            <form action=<?php echo "/start/logedin/editBlogpost/" . $blogpost->getId() ?> method="get">
                <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >Editera inlägget</button>
            </form>
            <form action="/deletePostFromDB" method="get">
                <button name="blogpost_id" value=<?php echo $blogpost->getId()?> >Tabort inlägget</button>
            </form>
        <?php endif ?>
        <article>
            <h1><?php echo $blogpost->getPostName() ?></h1>
            <p><?php echo 'Tags: ' . $tags?></p>
            <p><?php echo $blogpost->getContent() ?></p>
            <p><?php echo 'Skapad av: ' . $blogpost->getUsername() ?></p>
            <p><?php echo 'Datum: ' . $blogpost->getPostCreationTime() ?></p>
        </article>
        <?php if(isset($userId) && !empty($userId)): ?>
                <form action="/start/logedin/createComment" method="post">
                    <button name="blogpost_id" value=<?php echo $blogpost->getId()?>>Kommentera</button>
                </form>
        <?php endif ?>
        <?php if(!empty($comments)): ?>   
            <?php foreach($comments as $comment): ?>
                <article>
                    <p><?php echo $comment->getContent() ?></p>
                    <p><?php echo 'Kommentar av: ' . $comment->getUsername() ?></p>
                    <p><?php echo 'Datum: ' . $comment->getPostCreationTime() ?></p>
                </article>
                <?php if(isset($userId) && !empty($userId) && $userId === $comment->getUserId()): ?>
                    <form action=<?php echo "/start/logedin/editComment/" . $comment->getId() ?> method="get">
                        <button name="comment_id" value=<?php echo $comment->getId()?> >Editera</button>
                    </form>
                    <form action="/deleteCommentFromDB" method="get">
                        <button name="comment_id" value=<?php echo $comment->getId()?> >Tabort</button>
                    </form>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>
    <?php endforeach ?>
</section>