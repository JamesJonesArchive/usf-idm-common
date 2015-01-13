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


class UsfEncryption {

    private static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * @param string $key Encryption key
     * @param string $input Plaintext to encrypt
     * @param bool $filename_safe Use a filename-safe alphabet
     * @return string
     *
     * Generate a random initialization vector (IV) and encrypt data using AES256.  Combines the IV with the ciphertext
     * and Base64 encodes the results.
     */
    public static function encrypt($key, $input, $filename_safe = FALSE) {
        srand((double) microtime() * 1000000); //for MCRYPT_RAND
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $input = UsfEncryption::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CBC, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $base64Data = base64_encode($iv.$data);

        if ($filename_safe){
            return rtrim(strtr($base64Data, '+/', '-_'), '=');
        } else {
            return $base64Data;
        }
    }

    public static function decrypt($key, $input) {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CBC, '');

        // Split the IV from the ciphertext
        $iv = substr(base64_decode($input), 0, mcrypt_enc_get_iv_size($td));
        $cipher = substr(base64_decode($input), mcrypt_enc_get_iv_size($td), strlen($input));

        mcrypt_generic_init($td, $key, $iv);

        return mdecrypt_generic($td, $cipher);
    }
}