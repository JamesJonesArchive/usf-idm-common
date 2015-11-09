<?php

namespace USF\IdM;


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