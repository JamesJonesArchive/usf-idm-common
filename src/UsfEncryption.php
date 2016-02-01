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
 * @package PeopleSoftAuthenticator
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache2.0
 * @link https://github.com/USF-IT/PeopleSoftAuthenticator
 */
namespace USF\IdM;

use phpseclib\Crypt\Rijndael;
use phpseclib\Crypt\Random;
use MIME\Base64URLSafe;

/**
 * Encrypt data with AES
 *
 * @category USF/IT
 * @package usf-idm-coomon
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache2.0
 * @link https://github.com/USF-IT/usf-idm-common
 */
class UsfEncryption
{

    public static function encrypt($encryptionKey, $textInput, $blockType = 'CBC', $urlSafe = false) {
        switch ($blockType) {
            case 'CBC':
                $cipher = new Rijndael(Rijndael::MODE_CBC);
                $cipher->setKey($encryptionKey);
                $iv = Random::string($cipher->getBlockLength() >> 3);
                $cipher->setIV($iv);
                break;

            case 'ECB':
                $cipher = new Rijndael(Rijndael::MODE_ECB);
                $cipher->setKey($encryptionKey);
                $iv = '';
                break;

            default:
                throw new \Exception('Unknown encryption blocktype: '.$blockType, 500);
                break;
        }

        $encryptedResult = $iv.$cipher->encrypt($textInput);

        if ($urlSafe) {
            return Base64URLSafe::urlsafe_b64encode($encryptedResult);
        } else {
            return base64_encode($encryptedResult);
        }
    }

    public static function decrypt($encryptionKey, $encryptedString, $blockType = 'CBC', $urlSafe = false) {

        if ($urlSafe) {
            $data = Base64URLSafe::urlsafe_b64decode($encryptedString);
        } else {
            $data = base64_decode($encryptedString);
        }

        switch ($blockType) {
            case 'CBC':
                $cipher = new Rijndael(Rijndael::MODE_CBC);
                $cipher->setKey($encryptionKey);

                 // Split the IV from the ciphertext
                $iv = substr($data, 0, $cipher->getBlockLength() >> 3);
                $cipherText = substr($data, $cipher->getBlockLength() >> 3, strlen($encryptedString));

                $cipher->setIV($iv);
                break;

            case 'ECB':
                $cipher = new Rijndael(Rijndael::MODE_ECB);
                $cipher->setKey($encryptionKey);
                $cipherText = $data;
                break;

            default:
                throw new \Exception('Unknown encryption blocktype: '.$blockType, 500);
                break;
        }

        return $cipher->decrypt($cipherText);
    }
}

