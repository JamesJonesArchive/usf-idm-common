<?php

require_once ('../vendor/autoload.php');

use USF\IdM\UsfEncryption;

$string =  UsfEncryption::encrypt("12345678901234561234567890123456", "this is a test");

echo $string."\n";

echo UsfEncryption::decrypt("12345678901234561234567890123456", $string);


?>
