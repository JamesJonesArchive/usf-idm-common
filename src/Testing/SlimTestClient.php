<?php

namespace USF\IdM\Testing;

use \Slim;
/**
 * PHPUnit Test Client for Slim Framework Applications
 *
**/
class SlimTestClient
{
    public $app;
    public $request;
    public $response;

    // We support these methods for testing. These are available via
    // `this->get()` and `$this->post()`. This is accomplished with the
    // `__call()` magic method below.
    public $testingMethods = array('get', 'post', 'patch', 'put', 'delete', 'head');

    public function setupRequest($method, $path, $data = array(), $optionalHeaders = array())
    {
        // Capture STDOUT
        ob_start();

        $options = array(
            'REQUEST_METHOD' => strtoupper($method),
            'PATH_INFO'      => $path,
            'SERVER_NAME'    => 'local.dev'
        );

        if ($method === 'get') {
            $options['QUERY_STRING'] = http_build_query($data);
        } elseif (is_array($data)) {
            $options['slim.input']   = http_build_query($data);
        } else {
            $options['slim.input']   = $data;
        }

        // Prepare a mock environment
        Slim\Environment::mock(array_merge($options, $optionalHeaders));

    }

    // Abstract way to make a request to SlimPHP, this allows us to mock the
    // slim environment
    public function runRequest($app)
    {

        // Establish some useful references to the slim app properties
        $this->request  = $app->request();
        $this->response = $app->response();


        // Execute our app
        $app->run();

        // Return the application output. Also available in `response->body()`
        return ob_get_clean();
    }
}
