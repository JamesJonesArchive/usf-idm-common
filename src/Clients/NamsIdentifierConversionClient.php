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
    private $_rest_client;
    private $_nams_host;

    public function __construct(array $cas_config)
    {
        if ($this->_validateNamsConfig($cas_config)) {
            $this->_rest_client = new CasRestClient();
            $this->_rest_client->setCasServer($cas_config['cas_host']);
            $this->_rest_client->setCasRestContext('/v1/tickets');
            $this->_rest_client->verifySSL(false);
            $this->_rest_client->setCredentials($cas_config['cas_user'], $cas_config['cas_password']);
            $this->_rest_client->login($cas_config['ticket_storage']);
        }
    }

    public function setNamsHost($host)
    {
        $this->_nams_host = $host;
    }

    public function convertIdentifier($inputType, $outputType, $inputId)
    {
        if (empty($this->_nams_host)) {
            throw new \Exception('NAMS host not set!', 500);
        }

        $response = $this->_rest_client->get("https://$this->_nams_host/vip/services/ws_convert.php?submit_type=$inputType&return_type=$outputType&return=json&value=$inputId");

        $res_data = $response->json();
        if ($res_data['response'] == 'success') {
            return $res_data[$outputType];
        } else {
            return [];
        }
    }

    private function _validateNamsConfig(array $cas_config)
    {
        if (!isset($cas_config['cas_host'])) {
            throw new \Exception('CAS host not set!', 500);
        }
        if (!isset($cas_config['cas_user'])) {
            throw new \Exception('CAS user not set!', 500);
        }
        if (!isset($cas_config['cas_password'])) {
            throw new \Exception('CAS password not set!', 500);
        }
        if (!isset($cas_config['ticket_storage'])) {
            throw new \Exception('CAS Ticket storage not set!', 500);
        }

        return true;
    }
}

