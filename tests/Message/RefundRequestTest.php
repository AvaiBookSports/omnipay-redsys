<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Tests\TestCase;

class RefundRequestTest extends TestCase
{
    public function testGetData()
    {
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(
            [
                'merchantId' => '999008881',
                'terminalId' => '871',
                'amount' => '1.45',
                'currency' => 'EUR',
                'transactionId' => '0123abc',
                'hmacKey' => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
            ]
        );

        $data = $request->getData();

        $this->assertSame('999008881', $data['DATOSENTRADA']['DS_MERCHANT_MERCHANTCODE']);
        $this->assertSame('871', $data['DATOSENTRADA']['DS_MERCHANT_TERMINAL']);
        $this->assertSame('3', $data['DATOSENTRADA']['DS_MERCHANT_TRANSACTIONTYPE']);
        $this->assertSame(145, $data['DATOSENTRADA']['DS_MERCHANT_AMOUNT']);
        $this->assertSame('978', $data['DATOSENTRADA']['DS_MERCHANT_CURRENCY']);
        $this->assertSame('0123abc', $data['DATOSENTRADA']['DS_MERCHANT_ORDER']);

        $this->assertSame('HMAC_SHA256_V1', $data['DS_SIGNATUREVERSION']);
        // signature will change if undocumented fields added
        $this->assertSame('yXqkc11wm11PhQTosOH7TlPVE2w8IWH6iAqn/N6fb1w=', $data['DS_SIGNATURE']);
    }

    public function testGetHmacKey()
    {
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(
            [
                'merchantId' => '999008881',
                'terminalId' => '871',
                'amount' => '1.45',
                'currency' => 'EUR',
                'transactionId' => '0123abc',
                'hmacKey' => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
            ]
        );

        $this->assertSame('Mk9m98IfEblmPfrpsawt7BmxObt98Jev', $request->getHmacKey());
    }

    public function testGetDataTestMode()
    {
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(
            [
                'merchantId' => '999008881',
                'terminalId' => '871',
                'amount' => '1.45',
                'currency' => 'EUR',
                'transactionId' => '0123abc',
                'hmacKey' => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
            ]
        );

        $request->setTestMode(true);
        $this->assertSame('https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada', $request->getEndpoint());
        $request->setTestMode(false);
        $this->assertSame('https://sis.redsys.es/sis/services/SerClsWSEntrada', $request->getEndpoint());
    }
}
