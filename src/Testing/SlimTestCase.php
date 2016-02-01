<?php

namespace USF\IdM\Testing;

use USF\IdM\UsfConfig;

/**
 * PHPUnit Test Case for Slim Framework Applications.
 **/
class SlimTestCase extends \PHPUnit_Framework_TestCase
{
    // create a Slim instance
    public function getSlimInstance($configDir, $slimAppClassName)
    {
        // Create a config object
        $usfConfigObject = new UsfConfig($configDir);

        // Establish a local reference to the Slim app object
        return new $slimAppClassName($usfConfigObject);
    }
}
