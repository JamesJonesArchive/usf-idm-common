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
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_Message;

class UsfLogger {

    private $logger = [];
    private $name;
    private $defaultLogLevel = 'warn';

    public function __construct($name = 'log', $type = 'file'){
        $this->addLogger($name, $type, ['log_location' => '/var/log/usf-logger.log', 'log_level' => 'warn']);
    }

    public function addLogger($name, $type, $options = []){
        $this->name = $name;
        $this->logger[$name] = new Logger($name);
        $this->addLogHandler($type, $options);
    }

    public function addLogHandler($type, $options){
        // Throw exception if no handler configuration has been set. FirePHPHandler is the exception, it doesn't
        // have any config options.
        if ($options == []){
            throw new \Exception("Missing handler configuration!", 1);
        }

        // Set the log level if one wasn't given.
        if (! $options['log_level']) $options['log_level'] = $this->defaultLogLevel;

        // Add the requested handler
        switch ($type){
            case 'file':
                $this->logger[$this->name]->pushHandler(new StreamHandler($options['log_location'], $this->loggerLevel($options['log_level'])));
                break;

            case 'firebug':
                $this->logger[$this->name]->pushHandler(new FirePHPHandler());
                break;

            case 'syslog':
                $syslog = new SyslogHandler($options['facility'], $options['syslogLevel']);
                $formatter = new LineFormatter("%channel%.%level_name%: %message% %extra%");
                $syslog->setFormatter($formatter);
                $this->logger[$this->name]->pushHandler($syslog);
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

                $mailStream = new SwiftMailerHandler($mailer, $message, $this->loggerLevel($options['log_level']));
                $mailStream->setFormatter($htmlFormatter);
                $this->logger[$this->name]->pushHandler($mailStream);
                break;

            default:
                throw new \Exception("Unknown Log Handler", 1);
                break;
        }
    }

    public function setDefaultLogLevel($log_level){
        $this->defaultLogLevel = $log_level;
    }

    private function loggerLevel(){
        switch ($this->logLevel){
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

    public function __get($name){
        return $this->logger[$name];
    }
}