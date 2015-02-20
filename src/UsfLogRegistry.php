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
 * Class UsfLogRegistry
 *
 * Creates an array of UsfLogger objects.
 *
 * @package USF\IdM
 * @author Eric Pierce <epierce@usf.edu>
 * @copyright 2015 University of South Florida
 */
class UsfLogRegistry {

    /**
     * @var array Collection of registered logger objects
     */
    private $logRegistry = [];

    /**
     * @var string Default logging level used by UsfLogger Objects
     */
    private $defaultLogLevel = 'warn';

    private $defaultLogHandler = 'log';

    private static $instance;

    /**
     * Returns a Singleton instance of the log registry and add one logger to the registry.
     * Default values will add a file-based logger that writes to /var/log/usf-logger.log
     *
     * @param string $name
     * @param string $type
     */
    public static function getInstance(){

        if( self::$instance === null ){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add an UsfLogger object to the registry.
     *
     * @param string    $name     Name of the logging "channel"
     * @param string    $type     Log handler to use
     * @param array     $options  Configuration options for the log handler
     *
     * @throws \Exception
     */
    public function addLogger($name = 'log', $type = 'file', $options = ['log_location' => '/var/log/usf-logger.log', 'log_level' => 'warn']){
        if (array_key_exists($name,$this->logRegistry)){
            throw new \Exception('LogHandler named ['.$name.'] already exists.');
        }
        $this->logRegistry[$name] = new UsfLogger($name);
        $this->logRegistry[$name]->setDefaultLogLevel($this->defaultLogLevel);
        $this->logRegistry[$name]->addLogHandler($type, $options);

        //Add the web processor if this is not running on the command-line
        if (php_sapi_name() !== "cli") {
            $this->logRegistry[$name]->addLogProcessor('web');
        }
    }

    /**
     * Remove an UsfLogger object from the registry.
     *
     * @param string $name Name of the logging "channel"
     * @throws \Exception
     */
    public function removeLogger($name){
        if (! array_key_exists($name,$this->logRegistry)){
            throw new \Exception('LogHandler named ['.$name.'] does not exist');
        }

        unset($this->logRegistry['name']);
    }

    /**
     * Return an UsfLogger object from the registry.
     *
     * @param string $name Name of the logging "channel"
     * @return UsfLogger
     * @throws \Exception
     */
    public function getLogger($name){
        if (! array_key_exists($name,$this->logRegistry)){
            throw new \Exception('LogHandler named ['.$name.'] does not exist');
        }

        return $this->logRegistry['name'];
    }

    /**
     * Set the default logging level for log handlers added to registry.
     *
     * @param string $log_level
     */
    public function setDefaultLogLevel($log_level){
        $this->defaultLogLevel = $log_level;
    }

    /**
     * Set the default log handler.
     *
     * @param string $logHandler
     * @throws \Exception
     */
    public function setDefaultHandler($name){
        if (! array_key_exists($name,$this->logRegistry)){
            throw new \Exception('LogHandler named ['.$name.'] does not exist');
        }
        $this->defaultLogHandler = $name;
    }

    public function debug($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->debug($message, $context);
    }

    public function info($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->info($message, $context);
    }

    public function notice($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->notice($message, $context);
    }

    public function warn($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->warning($message, $context);
    }

    public function warning($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->warning($message, $context);
    }

    public function err($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->error($message, $context);
    }

    public function error($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->error($message, $context);
    }

    public function crit($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->critical($message, $context);
    }

    public function critical($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->critical($message, $context);
    }

    // For Slim framework
    public function fatal($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->critical($message, $context);
    }

    public function alert($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->alert($message, $context);
    }

    public function emerg($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->emergency($message, $context);
    }

    public function emergency($message, array $context = array()){
        return $this->logRegistry[$this->defaultLogHandler]->emergency($message, $context);
    }


    /**
     * "Magic" method for returning an entry in the registry as an object property.
     *
     * @param string $name
     * @return UsfLogger
     */
    public function __get($name){
        return $this->logRegistry[$name];
    }

}