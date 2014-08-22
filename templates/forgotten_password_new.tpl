[[+include file="header.tpl"]]

<div class="col-md-8">
    <h1>Nytt passord</h1>
    <p>Dersom du har glemt ditt passord kan du endre til nytt passord i dette skjemaet</p>

    <hr />

    <form action="" method="post" id="forgotten-password-new-form" name="forgotten-password-new-form">
        <div class="form-group">
            <label for="forgotten-password-new-form-password1">Passord <span style="color: red;">*</span></label>
            <input type="password" class="form-control" id="forgotten-password-new-form-password1" name="forgotten-password-new-form-password1" placeholder="Ditt passord her" />
            <p>Dette feltet er naturligvis påkrevd. <span id="forgotten-password-new-form-password-error1">Minimumslengde på 7 tegn</span>, blir hashet i databasen.</p>
        </div>

        <div class="form-group">
            <label for="forgotten-password-new-form-password2">Gjenta passord <span style="color: red;">*</span></label>
            <input disabled type="password" class="form-control" id="forgotten-password-new-form-password2" name="forgotten-password-new-form-password2" placeholder="Gjenta ditt passord her" />
            <p id="forgotten-password-new-form-password-error2">Det er en fordel om de to passord-feltene er like.</p>
        </div>

        <button type="submit" disabled id="forgotten-password-new-form-submit" class="btn btn-default">Send</button>
    </form>
</div>

[[+include file="footer.tpl"]]