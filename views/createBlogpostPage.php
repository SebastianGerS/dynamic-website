<form action="<?php echo $back?>" method="post">
    <button  class="btn btn-warning my-2" > Tillbaka</button>
</form>
<div class="form-group  py-3">
    <article id="create-blogpost-form"> 
        <form action="/postToDatabase" method="post">
            <input class="form-control my-3" name="post_name" type="text" placeholder="Titel" required autocomplete="off">
            <input class="form-control my-3" name="tagname" type="text" placeholder="Tagar" required autocomplete="off">
            <textarea class="form-control my-3" id="sizing-addon1" name ="content" type="text" placeholder="Inehåll" required autocomplete="off"></textarea>
            <button class="btn btn-success my-3"type="submit">Skapa blogpost</button>
        </form>  
    </article>
</div>