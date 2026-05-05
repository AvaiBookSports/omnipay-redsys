<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Tests\TestCase;

class RefundRequestTest extends TestCase
{
    private RefundRequest $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            [
                'merchantId' => '999008881',
                'terminalId' => '871',
                'amount' => '1.45',
                'currency' => 'EUR',
                'transactionId' => '0123abc',
                'hmacKey' => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
            ]
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

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
        $this->assertSame('Mk9m98IfEblmPfrpsawt7BmxObt98Jev', $this->request->getHmacKey());
    }

    public function testGetDataTestMode()
    {
        $this->request->setTestMode(true);
        $this->assertSame('https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada', $this->request->getEndpoint());
        $this->request->setTestMode(false);
        $this->assertSame('https://sis.redsys.es/sis/services/SerClsWSEntrada', $this->request->getEndpoint());
    }

    public function testSendDataSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $response = $this->request->send();

        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('999999', $response->getTransactionReference());
        $this->assertSame(900, (int) $response->getCode());
    }

    public function testSendDataError()
    {
        $this->setMockHttpResponse('RefundError.txt');

        $response = $this->request->send();

        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(180, (int) $response->getCode());
    }

    public function testSendDataEmptyReturnWrapsExceptionAsInvalidResponse()
    {
        $this->setMockHttpResponse('RefundEmptyReturn.txt');

        try {
            $this->request->send();
            $this->fail('Expected InvalidResponseException was not thrown');
        } catch (InvalidResponseException $e) {
            $this->assertSame(
                'Invalid response from payment gateway: could not decode response',
                $e->getMessage()
            );
            $this->assertNotNull($e->getPrevious(), 'Original exception should be chained as previous');
        }
    }
}
