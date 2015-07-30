[[+include file="header.tpl"]]

<div class="row">
    <div class="col-md-12">
        <h1>Innstillinger</h1>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#oversikt" data-toggle="tab">Oversikt</a></li>
            <li><a href="#informasjon" data-toggle="tab">Endre informasjon</a></li>
            <li><a href="#passord" data-toggle="tab">Endre passord</a></li>
        </ul>
    </div>
    <div class="tab-content">
      <div class="tab-pane active" id="oversikt">
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Generelt</h3>
                    </div>
                    <div class="panel-body">
                        <p><b>E-post:</b> [[+$PROFILE_USER_EMAIL]]</p>
                        <p><b>Kallenavn:</b> [[+$BASE_USER_NICK]]</p>
                        <p><b>Karma:</b> [[+$BASE_USER_KARMA]] / [[+$BASE_USER_KARMA_PENDING]]</p>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Annet</h3>
                    </div>
                    <div class="panel-body">
                        <p><b>Kan bidra:</b> [[+if $PROFILE_USER_CAN_CONTRIBUTE == 1]]<i class="fa fa-check" style="color: green;"></i>[[+else]]<i class="fa fa-times" style="color: red;"></i>[[+/if]]</p>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="tab-pane" id="informasjon">
            <div class="col-xs-12 col-md-6">
                <form action="" method="post" id="profile-edit-info-form">
                    <input type="hidden" value="info" name="source" />
                    <div class="form-group">
                        <label for="register-form-email">E-post <span style="color: red;">*</span></label>
                        <input type="email" class="form-control" data-original="[[+$PROFILE_USER_EMAIL]]" id="register-form-email" name="register-form-email" placeholder="Skriv din e-post her" value="[[+$PROFILE_USER_EMAIL]]" />
                        <p><span id="register-form-email-error1">Dette krever en gyldig e-post</span> og <span id="register-form-email-error2">e-posten kan ikke være i våre systemer fra før.</span></p>
                    </div>

                    <div class="form-group">
                        <label for="register-form-nick">Kallenavn</label>
                        <input type="text" class="form-control" id="register-form-nick" name="register-form-nick" placeholder="Skriv ønsket kallenavn her" value="[[+if $PROFILE_USER_NICK != '<em>Anonym</em>']][[+$PROFILE_USER_NICK]][[+/if]]" />
                        <p>La det stå tom for <em>Anonym</em>.</p>
                    </div>

                    <button type="submit" id="profile-edit-info-form-submit" class="btn btn-default">Lagre</button>
                </form>
            </div>
        </div>
        <div class="tab-pane" id="passord">
            <div class="col-xs-12 col-md-6">
                <form action="" method="post" id="forgotten-password-new-form" name="forgotten-password-new-form">
                    <input type="hidden" value="password" name="source" />
                    <div class="form-group">
                        <label for="forgotten-password-new-form-oldpassword">Gammelt passord <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" id="forgotten-password-new-form-oldpassword" name="forgotten-password-new-form-oldpassword" placeholder="Ditt gamle passord her" />

                        <label for="forgotten-password-new-form-password1">Nytt passord <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" id="forgotten-password-new-form-password1" name="forgotten-password-new-form-password1" placeholder="Ditt nye passord her" />
                        <p>Dette feltet er naturligvis påkrevd. <span id="forgotten-password-new-form-password-error1">Minimumslengde på 7 tegn</span>, blir hashet i databasen.</p>
                    </div>

                    <div class="form-group">
                        <label for="forgotten-password-new-form-password2">Gjenta nytt passord <span style="color: red;">*</span></label>
                        <input disabled type="password" class="form-control" id="forgotten-password-new-form-password2" name="forgotten-password-new-form-password2" placeholder="Gjenta ditt nye passord her" />
                        <p id="forgotten-password-new-form-password-error2">Det er en fordel om de to passord-feltene er like.</p>
                    </div>

                    <button type="submit" disabled id="forgotten-password-new-form-submit" class="btn btn-default">Send</button>
                </form>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>

[[+include file="footer.tpl"]]
</body>
</html>