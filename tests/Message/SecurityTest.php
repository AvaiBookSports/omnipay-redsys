<?php

namespace Omnipay\Redsys\Message;

use Mockery as m;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Tests\TestCase;

class SecurityTest extends TestCase
{
    protected $security;

    protected $mockSecurity;

    #[\Override]
    public function setUp(): void
    {
        $this->security = new Security();
        $this->mockSecurity = m::mock(Security::class);
    }

    public function tearDown(): void
    {
        m::close();
    }

    public function testEncodeMerchantParameters(): void
    {
        $this->assertSame(
            'eyJ0ZXN0X3RoaW5nIjoic29tZS1tZXNzYWdlIn0=',
            $this->security->encodeMerchantParameters(['test_thing' => 'some-message'])
        );
    }

    public function testDecodeMerchantParameters(): void
    {
        $this->assertSame(
            ['test_thing' => 'some-message'],
            $this->security->decodeMerchantParameters('eyJ0ZXN0X3RoaW5nIjoic29tZS1tZXNzYWdlIn0=')
        );
    }

    /**
     * Confirm that the openssl extension is loaded and we have the appropriate method.
     *
     * Checks are split out in case only one is failing, rather than the blanket true/false for both
     */
    public function testHasValidEncryptionMethod(): void
    {
        $this->assertTrue(\extension_loaded('openssl'));
        $this->assertTrue(\function_exists('openssl_encrypt'));
        $this->assertTrue($this->security->hasValidEncryptionMethod());
    }

    /**
     * Test successful message encryption.
     */
    public function testEncryptMessageSuccess(): void
    {
        $this->mockSecurity->expects('hasValidEncryptionMethod')->andReturns(true);
        $cipher = unpack('H*', (string) $this->encryptMessage());
        $this->assertSame('771c1265741bc77139c811410899bb11', $cipher[1]);
    }

    /**
     * Make sure correct exception fires when no valid extension is installed.
     */
    public function testEncryptMessageException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No valid encryption extension installed');
        $this->mockSecurity->expects('hasValidEncryptionMethod')->andReturns(false);
        $this->encryptMessage();
    }

    /**
     * Helper method to test protected encryptMessage() method.
     */
    protected function encryptMessage(): mixed
    {
        $class = new \ReflectionClass($this->mockSecurity);
        $method = $class->getMethod('encryptMessage');

        return $method->invokeArgs($this->mockSecurity, ['test message', 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev']);
    }

    /**
     * Test signature creation with XML string and order number.
     */
    public function testCreateSignature(): void
    {
        $message = '<DATOSENTRADA>'
            .'<DS_MERCHANT_AMOUNT>145</DS_MERCHANT_AMOUNT>'
            .'<DS_MERCHANT_ORDER>0123abc</DS_MERCHANT_ORDER>'
            .'<DS_MERCHANT_MERCHANTCODE>999008881</DS_MERCHANT_MERCHANTCODE>'
            .'<DS_MERCHANT_CURRENCY>978</DS_MERCHANT_CURRENCY>'
            .'<DS_MERCHANT_PAN>9999999999999999</DS_MERCHANT_PAN>'
            .'<DS_MERCHANT_CVV2>285</DS_MERCHANT_CVV2>'
            .'<DS_MERCHANT_TRANSACTIONTYPE>A</DS_MERCHANT_TRANSACTIONTYPE>'
            .'<DS_MERCHANT_TERMINAL>871</DS_MERCHANT_TERMINAL>'
            .'<DS_MERCHANT_EXPIRYDATE>2012</DS_MERCHANT_EXPIRYDATE>'
            .'</DATOSENTRADA>';
        $salt = '0123abc';
        $key = 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev';
        $signature = $this->security->createSignature($message, $salt, $key);
        $this->assertSame('eg0O+JA4rMHqmK1osLfOgin75MLpqfkLAMEJbCFrtw4=', $signature);
    }

    /**
     * Test return signature creation with XML string and order number.
     */
    public function testCreateReturnSignature(): void
    {
        $message = '<DATOSENTRADA>'
            .'<DS_MERCHANT_AMOUNT>145</DS_MERCHANT_AMOUNT>'
            .'<DS_MERCHANT_ORDER>0123abc</DS_MERCHANT_ORDER>'
            .'<DS_MERCHANT_MERCHANTCODE>999008881</DS_MERCHANT_MERCHANTCODE>'
            .'<DS_MERCHANT_CURRENCY>978</DS_MERCHANT_CURRENCY>'
            .'<DS_MERCHANT_PAN>9999999999999999</DS_MERCHANT_PAN>'
            .'<DS_MERCHANT_CVV2>285</DS_MERCHANT_CVV2>'
            .'<DS_MERCHANT_TRANSACTIONTYPE>A</DS_MERCHANT_TRANSACTIONTYPE>'
            .'<DS_MERCHANT_TERMINAL>871</DS_MERCHANT_TERMINAL>'
            .'<DS_MERCHANT_EXPIRYDATE>2012</DS_MERCHANT_EXPIRYDATE>'
            .'</DATOSENTRADA>';
        $salt = '0123abc';
        $key = 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev';
        $signature = $this->security->createReturnSignature($message, $salt, $key);
        $this->assertSame('eg0O-JA4rMHqmK1osLfOgin75MLpqfkLAMEJbCFrtw4=', $signature);
    }
}
