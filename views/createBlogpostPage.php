<div class="form-group  py-3">
    <article id="create-blogpost-form"> 
        <form action="/postToDatabase" method="post">
            <input class="form-control my-3" name="post_name" type="text" placeholder="Titel" required>
            <input class="form-control my-3" name="tagname" type="text" placeholder="Tagar" required>
            <textarea class="form-control my-3" id="sizing-addon1" name ="content" type="text" placeholder="Inehåll"  required></textarea>
            <button class="btn btn-success my-3"type="submit">Skapa blogpost</button>
        </form>  
    </article>
</div>