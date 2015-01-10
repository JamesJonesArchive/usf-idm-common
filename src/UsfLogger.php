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
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\SyslogHandler;

class UsfLogger {

    private $logger;
    private $name;
    private $location = '/var/log/usf-logger.log';
    private $logLevel = 'info';
    private $facility = 'usf-logger';
    private $syslogLevel = 'local6';

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

            default:
                throw new \Exception("Unknown Log Handler", 1);
                break;
        }
    }

    public function setLogLevel($level){
        $this->logLevel = $level;
    }

    public function setLocation($location){
        $this->location = $location;
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