<div class="login-wrap">
    <div class="content-wrap-login">
    <h2 class="sign-in-text top">Welcome Back!!!</h2>
    <div id="login"  class="form-block w-form">
        <form name="loginform" id="email-form" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')) ?>" method="post">
            <?php if (count($attributes['errors']) > 0) : ?>
                <div class="message">
                    <?php foreach ($attributes['errors'] as $error) : ?>
                        <p class="login-error">
                            <?php echo $error; ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div>
                <input placeholder="Email"  type="text" name="log" id="user_login" aria-describedby="login_error" class="checkbox w-input" value="" size="20" autocapitalize="off" autocomplete="username">
            </div>

            <div class="user-pass-wrap">
                <div class="wp-pwd">
                    <input placeholder="Password"  type="password" name="pwd" id="user_pass" aria-describedby="login_error" class="checkbox _2 w-input password-input" value="" size="20" autocomplete="current-password">
                </div>
            </div>
            <div class="remember-me">
                <input type="checkbox" id="remmber">
                <label for="remmber">Remember Me</label>
            </div>

            <div class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button w-button" value="LOG IN">
                <input type="hidden" name="redirect_to" value="<?php echo esc_url(site_url()) ?>">
                <input type="hidden" name="testcookie" value="1">
            </div>
            <div class="lost-pass text-center">
                <a href="<?php echo site_url('wp-login.php?action=lostpassword'); ?>" class="underline">Forgot your password?</a>
            </div>
        </form>
    </div>
    <div class="wrap-top-line">
        <h5 class="heading-32">Don&#x27;t have an account?
        <a href="/member-register" class="w-inline-block">
            <h4 class="heading-32">Sign up</h4>
        </a>
        </h5>
        
        </div>
    </div>
</div>

