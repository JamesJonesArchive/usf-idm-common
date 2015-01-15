<?php
/**
 * Copyright 2015 University of South Florida
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
namespace USF\IdM;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_Message;
use USF\IdM\LogHandlers\TwilioHandler;

/**
 * Class UsfLogger
 *
 * Extends Monolog ({@link https://github.com/Seldaek/monolog}) with default settings for USF IdM projects.  Includes
 * support for the following log handlers:
 * -File ({@link https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/StreamHandler.php})
 * -FirePHP ({@link https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/FirePHPHandler.php})
 * -Syslog ({@link https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/SyslogHandler.php})
 * -Email ({@link https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/SwiftMailHandler.php})
 * -SMS (TwilioHandler.php)
 *
 * @package USF\IdM
 * @author Eric Pierce <epierce@usf.edu>
 * @copyright 2015 University of South Florida
 */
class UsfLogger extends Logger{

    /**
     * @var string Default logging level
     */
    private $defaultLogLevel = 'warn';

    /**
     * Wraps the creation of log handlers so they can be created in a standard way.
     *
     * @param string $type     Type of log handler to create
     * @param array  $options  Configuration options for log handler
     *
     * @throws \Exception
     */
    public function addLogHandler($type, $options){
        // Throw exception if no handler configuration has been set. FirePHPHandler is the exception, it doesn't
        // have any config options.
        if ($options == []){
            throw new \Exception("Missing handler configuration!", 1);
        }

        // Set the log level if one wasn't given.
        if (! array_key_exists('log_level',$options)) $options['log_level'] = $this->defaultLogLevel;

        // Allow messages to "bubble-up" the stack to other handlers
        if (! array_key_exists('bubble',$options)) $options['bubble'] = true;

        // Add the requested handler
        switch ($type){
            case 'file':
                $this->pushHandler(new StreamHandler($options['log_location'],
                                                     $this->loggerLevel($options['log_level']),
                                                     $options['bubble']
                                                    )
                );
                break;

            case 'firebug':
                $this->pushHandler(new FirePHPHandler($this->loggerLevel($options['log_level']),$options['bubble']));
                break;

            case 'syslog':
                $syslog = new SyslogHandler($options['facility'], $options['syslogLevel'], $options['bubble']);
                $formatter = new LineFormatter("%channel%.%level_name%: %message% %context% %extra%");
                $syslog->setFormatter($formatter);
                $this->pushHandler($syslog);
                break;

            case 'mail':
                // Create the Swift_mail transport
                $transport = Swift_SmtpTransport::newInstance(
                    $options['host'],
                    $options['port'], 'ssl')
                        ->setUsername(
                            $options['username'])
                        ->setPassword(
                            $options['password']);

                // Create the Mailer using your created Transport
                $mailer = Swift_Mailer::newInstance($transport);

                // Create a message
                $message = Swift_Message::newInstance($options['subject'])
                    ->setFrom($options['from'])
                    ->setTo($options['to'])
                    ->setBody('', 'text/html');

                $htmlFormatter = new HtmlFormatter();

                $mailStream = new SwiftMailerHandler($mailer,
                                                     $message,
                                                     $this->loggerLevel($options['log_level']),
                                                     $options['bubble']
                );
                $mailStream->setFormatter($htmlFormatter);
                $this->pushHandler($mailStream);
                break;

            case 'sms':
                $twilio = new TwilioHandler($options['account_sid'],
                                            $options['auth_token'],
                                            $options['from_numbers'],
                                            $options['to_numbers'],
                                            $options['log_level'],
                                            $options['bubble']
                );

                $formatter = new LineFormatter("%channel%.%level_name%: %message% %context%");
                $twilio->setFormatter($formatter);

                $this->pushHandler($twilio);
                break;

            default:
                throw new \Exception("Unknown Log Handler", 1);
                break;
        }
    }

    /**
     * Wraps the creation of log processors so they can be created in a standard way.
     *
     * @param string $type     Type of log processor to add
     *
     * @throws \Exception
     */
    public function addLogProcessor($type){
        switch ($type) {
            case 'web':
                $this->pushProcessor(new WebProcessor());
                break;

            case 'intro':
            case 'introspection':
                $this->pushProcessor(new IntrospectionProcessor());
                break;

            default:
                throw new \Exception("Unknown Log Processor", 1);
                break;
        }

    }

    /**
     * Set the default logging level for this handler
     *
     * @param string $log_level Logging level
     */
    public function setDefaultLogLevel($log_level){
        $this->defaultLogLevel = $log_level;
    }

    /**
     * Get the Monolog constants for log levels.
     *
     * @param  string $level Log level name (debug, info, etc)
     * @return int
     * @throws \Exception
     */
    private function loggerLevel($level){
        switch ($level){
            case 'trace':
            case 'debug':
                return Logger::DEBUG;
                break;

            case 'info':
                return Logger::INFO;
                break;

            case 'notice':
                return Logger::NOTICE;
                break;

            case 'warn':
            case 'warning':
                return Logger::WARNING;
                break;

            case 'error':
                return Logger::ERROR;
                break;

            case 'crit':
            case 'critical':
                return Logger::CRITICAL;
                break;

            case 'alert':
                return Logger::ALERT;
                break;

            case 'emergency':
                return Logger::EMERGENCY;
                break;

            default:
                throw new \Exception("Unknown Log Level", 1);
                break;
        }

    }

}