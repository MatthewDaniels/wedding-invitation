<?php

use RedBean_Facade as R;

if($env == 'production'){
    R::setup('mysql:host=127.0.0.1;dbname=wedding_invitation_db','wd_db','H9df2P1Hwox*');
}

$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enabled' => true,
        'debug' => false,
        'cache' => realpath('../tmp/cache')
    ));
});
