<?php 

function getDatabaseConfig() : array
{
    return [
        'database' => [
            'local' => [
                'url' => 'mysql:host=localhost:3316;dbname=php_login_management_test',
                'username' => 'root',
                'password' => ''
            ],
            'production' => [
                'url' => 'mysql:host=localhost:3316;dbname=php_login_management',
                'username' => 'root',
                'password' => ''
            ]
        ]
    ];
}

?>