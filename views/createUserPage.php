
<div class="form-group py-3">
    <article id="create-user-form"> 
        <form action="/createUser" method="post">
            <input class="form-control my-3" name="firstname" type="text" placeholder="Förnamn" required>
            <input class="form-control my-3"name="surname" type="text" placeholder="Efternamn" required>
            <input class="form-control my-3"name="email" type="text" placeholder="Epost" required>
            <input class="form-control my-3"name="username" type="text" placeholder="Användarnamn" required>
            <input class="form-control my-3"name="password" type="text" placeholder="Lösenord" required>
            <button class="btn btn-success" type="submit">Skapa användare</button>
        </form>  
    </article>
</div>