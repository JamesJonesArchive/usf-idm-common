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

    private $logger;
    private $name;
    private $mailConfig;
    private $location = '/var/log/usf-logger.log';
    private $logLevel = 'info';
    private $facility = 'usf-logger';
    private $syslogLevel = 'local6';
    private $from = ['root@localhost' => 'USF IdM Admin'];
    private $to = [];
    private $subject = 'Log Message';

    public function __construct($name = 'log', $type = 'file'){
        $this->name = $name;
        $this->logger = new Logger($name);
        $this->addLogHandler($type);
    }

    public function addLogHandler($type){
        switch ($type){
            case 'file':
                $this->logger->pushHandler(new StreamHandler($this->location, $this->loggerLevel($this->logLevel)));
                break;

            case 'firebug':
                $this->logger->pushHandler(new FirePHPHandler());
                break;

            case 'syslog':
                $syslog = new SyslogHandler($this->facility, $this->syslogLevel);
                $formatter = new LineFormatter("%channel%.%level_name%: %message% %extra%");
                $syslog->setFormatter($formatter);
                $this->logger->pushHandler($syslog);
                break;

            case 'mail':
                // Throw exception if no email address has been set.
                if ($this->to == []){
                    throw new \Exception("No email addresses have been set!", 1);
                }

                // Create the Swift_mail transport
                $transport = Swift_SmtpTransport::newInstance(
                    $this->mailConfig['host'],
                    $this->mailConfig['port'], 'ssl')
                        ->setUsername(
                            $this->mailConfig['username'])
                        ->setPassword(
                            $this->mailConfig['password']);

                // Create the Mailer using your created Transport
                $mailer = Swift_Mailer::newInstance($transport);

                // Create a message
                $message = Swift_Message::newInstance($this->subject)
                    ->setFrom($this->from)
                    ->setTo($this->to)
                    ->setBody('', 'text/html');

                $htmlFormatter = new HtmlFormatter();

                $mailStream = new SwiftMailerHandler($mailer, $message, Logger::WARNING);
                $mailStream->setFormatter($htmlFormatter);
                $this->logger->pushHandler($mailStream);
                break;

            default:
                throw new \Exception("Unknown Log Handler", 1);
                break;
        }
    }

    public function addTo($toAddress){
        if(is_array($toAddress)){
            $this->to[] = $this->to + $toAddress;
        } else {
            $this->to[] = $toAddress;
        }
    }

    public function setFrom($fromArray){
        $this->from = $fromArray;
    }

    public function setSubject($subject){
        $this->subject = $subject;
    }

    public function setLogLevel($level){
        $this->logLevel = $level;
    }

    public function setLocation($location){
        $this->location = $location;
    }

    public function setMailConfig($mailConfig){
        $this->mailConfig = $mailConfig;
    }

    private function loggerLevel(){
        switch ($this->logLevel){
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
                return Logger::WARNING;
                break;

            case 'error':
                return Logger::ERROR;
                break;

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
        return $this->logger;
    }
}