<?php

require_once ('../vendor/autoload.php');

use USF\IdM\UsfConfig;

// Read all of the config files in ./config
$config = new UsfConfig('./config');

// Display one of the config arrays
print_r($config->mailConfig);


/* The config is equivalent to this PHP code:

$mailConfig = [
    'log_level' => 'warn',
    'host' => 'smtp.gmail.com',
    'port' => 465,
    'username' => 'it-janrain@mail.usf.edu',
    'password' => '',
    'subject' => 'Log Message',
    // Who the message will be from
    'from' => ['root@localhost' => 'USF IdM Admin'],
    // one or more email addresses the logs will go to
    'to' => ['cims-tech-core@mail.usf.edu']
];
*/

?>
