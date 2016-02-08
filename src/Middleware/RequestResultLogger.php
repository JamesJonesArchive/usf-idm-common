<?php
/**
 *   Copyright 2015 University of South Florida
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 * @category USF/IT
 * @package usf-idm-common
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache2.0
 * @link https://github.com/USF-IT/usf-idm-common
 */
namespace USF\IdM\Middleware;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Log the results of a Slim request
 *
 * @category USF/IT
 * @package usf-idm-common
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache2.0
 * @link https://github.com/USF-IT/usf-idm-common
 */
class RequestResultLogger
{
    private $logger;

    /**
     * Class constructor
     *
     * @param LoggerInterface $logger Log object
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        // Start the timer at the beginning of the request
        \PHP_Timer::start();
        $response = $next($request, $response);

        // Log the results
        $this->logger->info(
            $request->getUri()->getAuthority().' '.
            $request->getHeaderLine('AUTH_PRINCIPAL').' '.
            $request->getAttribute('ip_address').' '.
            $request->getMethod().' '.
            $request->getUri()->getPath().' '.
            $response->getStatusCode().' '.
            $request->getBody()->getSize().' '.
            $response->getBody()->getSize().' '.
            \PHP_Timer::stop()
        );

        return $response;
    }
}
