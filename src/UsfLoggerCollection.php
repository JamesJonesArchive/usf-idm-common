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

/**
 * Class UsfLoggerCollection
 *
 * Creates an array of UsfLogger objects.
 *
 * @package USF\IdM
 * @author Eric Pierce <epierce@usf.edu>
 * @copyright 2015 University of South Florida
 */
class UsfLoggerCollection {

    /**
     * @var array Collection of registered logger objects
     */
    private $logger = [];

    /**
     * @var string Default logging level used by UsfLogger Objects
     */
    private $defaultLogLevel = 'warn';

    /**
     * Object constructor.  Default values will create a file-based logger that writes to /var/log/usf-logger.log
     *
     * @param string $name
     * @param string $type
     */
    public function __construct($name = 'log', $type = 'file'){
        $this->addLogger($name, $type, ['log_location' => '/var/log/usf-logger.log', 'log_level' => 'warn']);
    }

    /**
     * Add a UsfLogger object to the current collection.
     *
     * @param string    $name     Name of the logging "channel"
     * @param string    $type     Log handler to use
     * @param array     $options  Configuration options for the log handler
     *
     * @throws \Exception
     */
    public function addLogger($name, $type, $options = []){
        $this->logger[$name] = new UsfLogger($name);
        $this->logger[$name]->setDefaultLogLevel($this->defaultLogLevel);
        $this->logger[$name]->addLogHandler($type, $options);
    }

    /**
     * @param $log_level
     */
    public function setDefaultLogLevel($log_level){
        $this->defaultLogLevel = $log_level;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name){
        return $this->logger[$name];
    }
}