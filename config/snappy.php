<?php

$environment = env('APP_ENV');

return array(

    'pdf' => array(
        'enabled' => true,
        'binary'  => $environment === 'local' || $environment === 'dev' ? '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"' : '/usr/local/bin/wkhtmltopdf',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary'  => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);
