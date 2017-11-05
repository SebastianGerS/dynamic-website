<section>  
    <article id="create-blogpost-form"> 
        <form action="/postToDatabase" method="post">
            <input name="post_name" type="text" placeholder="Titel" required>
            <input name="tagname" type="text" placeholder="Tags" required>
            <input id="content" name ="content" type="text"  required>
            <button type="submit">Skapa blogpost</button>
        </form>  
    </article>
</section>