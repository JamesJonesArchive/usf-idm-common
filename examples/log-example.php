<?php

require_once ('../vendor/autoload.php');

use USF\IdM\UsfLogRegistry;
use USF\IdM\UsfConfig;

$config = new UsfConfig('config');

// Create an instance of UsfLogRegistry
$logger = UsfLogRegistry::getInstance();

// Calling addLogger with no options creates a loghandler named 'log' that writes messages to /var/log/usf-logger.log
$logger->addLogger();

// Add a log processor that logs the method, class, file and line number of the call
$logger->log-addLogProcessor('introspection');

// log an error message with extra array data
$logger->log->warn('This is a test message.', ['foo' => 'bar']);

// Add a logger that emails audit reports
$logger->addLogger('audit', 'mail', $config->mailConfig);

//Send an email
$logger->audit->warn('Audit Test', ['foo' => 'bar', 'black' => 'white', 'yes' => 'no']);

// Add an additional handler to the 'audit' logger that will send text messages through Twilio
$logger->audit->addLogHandler('sms',$config->smsConfig);

//This message will be emailed AND sent as a text
$logger->audit->critical('Critical Problem!!', ['var1' => 'true', 'var2' => 'false']);


?>
