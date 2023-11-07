<div class="login-wrap">
    <div class="content-wrap-login">
    <h2 class="sign-in-text top">Create New Account</h2>
    <div id="login"  class="form-block w-form">
        <form name="loginform" id="email-form" action="<?php echo wp_registration_url(); ?>" method="post">         
            <input type="hidden" name="role"> 
            <div>
                <input placeholder="First name" maxlength="256" type="text" name="first_name" id="first_name" aria-describedby="login_error" class="checkbox w-input" value="" size="20" autocapitalize="off">
            </div>
            <div>
                <input placeholder="Surname" maxlength="256" type="text" name="surname" id="surname" aria-describedby="login_error" class="checkbox w-input" value="" size="20" autocapitalize="off">
            </div>
            <div>
                <input placeholder="Email" maxlength="256" type="text" name="email" id="user_login" aria-describedby="login_error" class="checkbox w-input" value="" size="20" autocapitalize="off" autocomplete="username">
            </div>

            <div>
                <input placeholder="Password" maxlength="256" type="password" class="checkbox _2 w-input password-input" name="pass1">
            </div>

            <div>
                <input placeholder="Repeat Password" maxlength="256" class="checkbox _2 w-input password-input" type="password" name="pass2">
            </div>

            <div class="submit">
                <input type="submit" name="submit" class="register-button button w-button"
                            value="Sign up"/>
            </div>
        </form>
    </div>
    <div class="wrap-top-line">
        <h5 class="heading-32">Already have an account?</h5>
        <a href="/member-login" class="w-inline-block">
            <h4 class="heading-32">Sign in</h4>
        </a>
    </div>
</div>