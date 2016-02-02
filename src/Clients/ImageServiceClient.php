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

class ImageServiceClient {

    public static function getImageURL($imageServiceScheme, $imageServiceHost, $imageServicePort, $imageServicePath, $identifier, $keyName, $keyData, $separator = '|'){
        // Create the plaintext containing the current time and the Unumber requested
        $plaintext = time() . $separator . $identifier;
        //Encrypt the token
        $encryptedToken = UsfEncryption::encrypt($plaintext, $keyData);
        return $imageServiceScheme.'://'.$imageServiceHost.':'.$imageServicePort.$imageServicePath.'/view/'.$keyName.'/'.urlencode($encryptedToken).'.jpg';
    }

    public static function getResizedImageURL($imageServiceScheme, $imageServiceHost, $imageServicePort, $imageServicePath, $identifier, $keyName, $keyData, $width, $height, $separator = '|'){
        // Create the plaintext containing the current time and the Unumber requested
        $plaintext = time() . $separator . $identifier;
        //Encrypt the token
        $encryptedToken = UsfEncryption::encrypt($plaintext, $keyData);
        return $imageServiceScheme.'://'.$imageServiceHost.':'.$imageServicePort.$imageServicePath.'/view/'.$keyName.'/'.$width.'/'.$height.'/'.urlencode($encryptedToken).'.jpg';
    }

}
