<?php

namespace USF\IdM;

class UsfEncryptionTest extends \PHPUnit_Framework_TestCase
{

    public function testEncrypt_AES128_CBC_URLunsafe()
    {
        $key = 'abcdefghijklmnop';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text);
        $result = UsfEncryption::decrypt($key, $encrypt);

        $this->assertNotEquals($text, $encrypt);
        $this->assertEquals($text, $result);
    }

    public function testEncrypt_AES256_CBC_URLunsafe()
    {
        $key = 'abcdefghijklmnop1234567890123456';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text);
        $result = UsfEncryption::decrypt($key, $encrypt);

        $this->assertNotEquals($text, $encrypt);
        $this->assertEquals($text, $result);
    }

    public function testEncrypt_AES256_CBC_URLsafe()
    {
        $key = 'abcdefghijklmnop1234567890123456';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text, 'CBC', true);
        $result = UsfEncryption::decrypt($key, $encrypt, 'CBC', true);

        $this->assertNotEquals($text, $encrypt);
        $this->assertEquals($text, $result);
    }

    public function testEncrypt_AES128_CBC_URLsafe()
    {
        $key = '1234567890123456';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text, 'CBC', true);
        $result = UsfEncryption::decrypt($key, $encrypt, 'CBC', true);

        $this->assertNotEquals($text, $encrypt);
        $this->assertEquals($text, $result);
    }

    public function testEncrypt_AES128_ECB_URLunsafe()
    {
        $key = 'abcdefghijklmnop';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text, 'ECB');
        $result = UsfEncryption::decrypt($key, $encrypt, 'ECB');

        $this->assertNotEquals($text, $encrypt);
        $this->assertEquals($text, $result);
    }

    public function testEncrypt_AES256_ECB_URLunsafe()
    {
        $key = 'abcdefghijklmnop1234567890123456';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text, 'ECB');
        $result = UsfEncryption::decrypt($key, $encrypt, 'ECB');

        $this->assertNotEquals($text, $encrypt);
        $this->assertEquals($text, $result);
    }

    public function testEncrypt_AES256_ECB_URLsafe()
    {
        $key = 'abcdefghijklmnop1234567890123456';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text, 'ECB', true);
        $result = UsfEncryption::decrypt($key, $encrypt, 'ECB', true);

        $this->assertNotEquals($text, $encrypt);
        $this->assertEquals($text, $result);
    }

    public function testEncrypt_AES128_ECB_URLsafe()
    {
        $key = '1234567890123456';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text, 'ECB', true);
        $result = UsfEncryption::decrypt($key, $encrypt, 'ECB', true);

        $this->assertNotEquals($text, $encrypt);
        $this->assertEquals($text, $result);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Unknown encryption blocktype: BAD
     */
    public function testEncrypt_BadBlockType()
    {
        $key = '1234567890123456';
        $text = 'This is a test';

        $encrypt = UsfEncryption::encrypt($key, $text, 'BAD');

    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Unknown encryption blocktype: BAD
     */
    public function testDecrypt_BadBlockType()
    {
        $key = '1234567890123456';
        $text = 'ruuamTBmln1hMdmVGazmk/Vw4x5raFpZGYOO5Yw+Ac6dpDtVWB6AmCfyJtWQVDbbhIIeae6OmDifMNR5AaD1S]Q==';

        $encrypt = UsfEncryption::decrypt($key, $text, 'BAD');

    }

    public function testDecrypt()
    {
        $key = '1234567891234567';
        $text = '1348339338|GEMS|127.0.0.1|EPIERCE';
        $encrypt = 'ruuamTBmln1hMdmVGazmk/Vw4x5raFpZGYOO5Yw+Ac6dpDtVWB6AmCfyJtWQVDbbhIIeae6OmDifMNR5AaD1S]Q==';

        $result = UsfEncryption::decrypt($key, $encrypt);

        $this->assertEquals($text, $result);

    }
}
