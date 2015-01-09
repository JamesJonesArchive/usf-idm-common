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

    private $loggers = [];

    public function __construct($name, $location){
        // Create a file-based log channel
        $this->loggers[$name] = $this->addStreamHandler($name, $location);
    }

    public function addStreamHandler($name, $location){
        if (! array_key_exists($name, $this->loggers)){
            $this->loggers[$name] = new Logger($name);
        }
        $this->loggers[$name]->pushHandler(new StreamHandler($location, Logger::INFO));
    }

    public function addFirePHPHandler($name){
        if (! array_key_exists($name, $this->loggers)){
            $this->loggers[$name] = new Logger($name);
        }
        $this->loggers[$name]->pushHandler(new FirePHPHandler());
    }

    public function addSyslogHandler($name, $facility, $level){
        if (! array_key_exists($name, $this->loggers)){
            $this->loggers[$name] = new Logger($name);
        }
        $syslog = new SyslogHandler($facility, $level);
        $formatter = new LineFormatter("%channel%.%level_name%: %message% %extra%");
        $syslog->setFormatter($formatter);
        $this->loggers[$name]->pushHandler($syslog);
    }

    public function debug($name, $message, $context = []){
        return $this->loggers[$name]->addDebug($message, $context);
    }

    public function info($name, $message, $context = []){
        return $this->loggers[$name]->addInfo($message, $context);
    }

    public function notice($name, $message, $context = []){
        return $this->loggers[$name]->addNotice($message, $context);
    }

    public function warn($name, $message, $context = []){
        return $this->loggers[$name]->addWarning($message, $context);
    }

    public function error($name, $message, $context = []){
        return $this->loggers[$name]->addError($message, $context);
    }

    public function critical($name, $message, $context = []){
        return $this->loggers[$name]->addCritical($message, $context);
    }

    public function alert($name, $message, $context = []){
        return $this->loggers[$name]->addAlert($message, $context);
    }

    public function emergency($name, $message, $context = []){
        return $this->loggers[$name]->addEmergency($message, $context);
    }


    public function __call($name, $arguments){
        return $this->$arguments[0]($name, $arguments[1], $arguments[2]);
    }
}