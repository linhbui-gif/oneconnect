<div class="login-wrap">
    <div class="content-wrap-login">
        <div id="password-lost-form" class="widecolumn">
            <?php if ($attributes['show_title']) : ?>
                <h3><?php _e('Forgot Your Password?', 'personalize-login'); ?></h3>
            <?php endif; ?>

            <?php if ($attributes['lost_password_sent']) : ?>
                <p class="login-info">
                    <?php _e('Check your email for a link to reset your password.', 'personalize-login'); ?>
                </p>
            <?php endif; ?>

            <?php if (count($attributes['errors']) > 0) : ?>
                <?php foreach ($attributes['errors'] as $error) : ?>
                    <p>
                        <?php echo $error; ?>
                    </p>
                <?php endforeach; ?>
            <?php endif; ?>

            <p>
                <?php
                _e(
                    "Enter your email address and we'll send you a link you can use to pick a new password.",
                    'personalize-login'
                );
                ?>
            </p>

            <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(home_url("/member-password-reset")); ?>" method="post">
                <p class="form-row">
                    <label class="checkbox-label" for="user_login"><?php _e('Email', 'personalize-login'); ?>
                    <input class="checkbox w-input" type="text" name="user_login" id="user_login">
                </p>

                <p class="lostpassword-submit">
                    <input type="submit" name="submit" class="button w-button" value="<?php _e('Reset Password', 'personalize-login'); ?>" />
                </p>
            </form>
        </div>
    </div>
</div>

