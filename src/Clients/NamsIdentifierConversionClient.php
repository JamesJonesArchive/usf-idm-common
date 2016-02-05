<?php

/**
 * Copyright 2015 University of South Florida.
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
 */
namespace USF\IdM\CLients;

use epierce\CasRestClient;

class NamsIdentifierConversionClient
{
    private $restClient;
    private $namsHost;

    public function __construct(array $casConfig)
    {
        if ($this->validateNamsConfig($casConfig)) {
            $this->restClient = new CasRestClient();
            $this->restClient->setCasServer($casConfig['cas_host']);
            $this->restClient->setCasRestContext('/v1/tickets');
            $this->restClient->verifySSL(false);
            $this->restClient->setCredentials($casConfig['cas_user'], $casConfig['cas_password']);
            $this->restClient->login($casConfig['ticket_storage']);
        }
    }

    public function setNamsHost($host)
    {
        $this->namsHost = $host;
    }

    public function convertIdentifier($inputType, $outputType, $inputId)
    {
        if (empty($this->namsHost)) {
            throw new \Exception('NAMS host not set!', 500);
        }

        $response = $this->restClient->get("https://$this->namsHost/vip/services/ws_convert.php?submit_type=$inputType&return_type=$outputType&return=json&value=$inputId");

        $responseData = $response->json();
        if ($responseData['response'] == 'success') {
            return $responseData[$outputType];
        }
        return [];
    }

    public function searchByName($searchTerm)
    {
        if (empty($this->namsHost)) {
            throw new \Exception('NAMS host not set!', 500);
        }

        $response = $this->restClient->get("https://$this->namsHost/vip/services/vip_person_search.php?value=$searchTerm");

        $responseData = json_decode($response->getBody(), true);
        if ($responseData['response'] == 'success') {
            return $responseData;
        }

        return [];
    }

    private function validateNamsConfig(array $casConfig)
    {
        if (!isset($casConfig['cas_host'])) {
            throw new \Exception('CAS host not set!', 500);
        }
        if (!isset($casConfig['cas_user'])) {
            throw new \Exception('CAS user not set!', 500);
        }
        if (!isset($casConfig['cas_password'])) {
            throw new \Exception('CAS password not set!', 500);
        }
        if (!isset($casConfig['ticket_storage'])) {
            throw new \Exception('CAS Ticket storage not set!', 500);
        }

        return true;
    }
}
