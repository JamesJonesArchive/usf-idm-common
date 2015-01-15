<?php

require_once ('../vendor/autoload.php');

use USF\IdM\UsfEncryption;

//AES-256 requires a 32-character key
$key = "12345678901234561234567890123456";

$crypt = UsfEncryption::encrypt($key, "this is a test");

echo "Encrypted string: ".$crypt."\n";

echo "Decrypted string: ".UsfEncryption::decrypt($key, $crypt);


?>
