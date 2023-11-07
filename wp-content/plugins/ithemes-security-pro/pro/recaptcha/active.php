<?php

require_once( __DIR__ . '/class-itsec-recaptcha.php' );
require_once( __DIR__ . '/API.php' );
$itsec_recaptcha = new ITSEC_Recaptcha();
$itsec_recaptcha->run();
