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

use \Slim\Slim;
use \Exception;

/**
 * Class SlimLogMiddleware
 *
 * Slim Framework Middleware for logging to Monolog.
 *
 * @package USF\IdM
 * @author Eric Pierce <epierce@usf.edu>
 * @copyright 2015 University of South Florida
 */
class SlimLogMiddleware extends \Slim\Middleware{

    public function call()
    {

        // Get reference to application
        $app = $this->app;

        // The Slim error handler doesn't work for middleware, so we have to wrap everything in a try/catch.
        // See https://github.com/codeguy/Slim/issues/841
        try {

            $app->container->singleton ('log', function ($c) {
                $env = $c['environment'];

                $logger = new UsfLogRegistry();
                if(is_array($env['log.config'])) {

                    $logger_list = [];

                    // Setup the loggers and add them to the registry
                    foreach($env['log.config'] as $log_writer) {
                        // Does this logger already exist?
                        if (in_array($log_writer['name'], $logger_list )) {
                            // Add a handler to an existing logger
                            $logger->$log_writer['name']->addLogHandler($log_writer['type'], $log_writer['config']);
                        } else {
                            // Create a new logger
                            $logger->addLogger ($log_writer['name'], $log_writer['type'], $log_writer['config']);
                            $logger_list[] = $log_writer['name'];
                        }
                        //Add Log processors
                        if(array_key_exists('processor',$log_writer))
                            $logger->$log_writer['name']->addLogProcessor ($log_writer['processor']);
                        //Set default handler
                        if(array_key_exists('default',$log_writer) && $log_writer['default'] == true){
                            $logger->setDefaultHandler($log_writer['name']);
                            $env['slim.log.default'] = $log_writer['name'];
                        }
                    }
                    $env['slim.log'] = $logger;

                    //If the user didn't specify a default handler, use the first one in the config array
                    if (empty($env['slim.log.default'])){
                        $logger->setDefaultHandler($env['log.config'][0]['name']);
                        $env['slim.log.default'] = $env['log.config'][0]['name'];
                    }

                    return $logger;
                } else {
                    throw \Exception('Environmental config "log.config" required!');
                }
            });

            // Run inner middleware and application
            $this->next->call();
        } catch (Exception $e) {
            $this->handleException($app, $e);
        }
    }

    /**
     * Return HTTP status code and message to requester
     *
     * @param Slim $app
     * @param Exception $e
     * @return mixed
     */
    private function handleException($app, Exception $e)
    {
        $status = $e->getCode();
        $statusText = \Slim\Http\Response::getMessageForCode($status);

        if ($statusText === null) {
            $status = 500;
            $statusText = 'Internal Server Error';
        }

        $app->response->setStatus($status);
        $app->response->headers->set('Content-Type', 'application/json');
        $app->response->setBody(json_encode(array(
            'status' => $status,
            'statusText' => preg_replace('/^[0-9]+ (.*)$/', '$1', $statusText),
            'description' => $e->getMessage(),
        )));

        $app->response()->finalize();
        return $app->response();
    }
}