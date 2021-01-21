<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Tests\TestCase;

class RefundResponseTest extends TestCase
{
    /** @var RefundResponse */
    private $response;

    public function testUnknowLanguage()
    {
        $this->getMockRequest()
            ->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev')
            ->shouldReceive('getParameters')->andReturn(['language' => 'foo']);

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => '2a9N3aJuXItx/+OtgycJAcuibhDAEOdsKFPqhkKKO8Y=',
                    'Ds_MerchantCode' => '999008881',
                    'Ds_Terminal' => '871',
                    'Ds_Response' => '0900',
                    'Ds_AuthorisationCode' => '999999',
                    'Ds_TransactionType' => '3',
                    'Ds_SecurePayment' => '2',
                    'Ds_Language' => '2',
                    'Ds_Card_Country' => '724',
                    'Ds_Card_Brand' => '1',
                    'Ds_ProcessedPayMethod' => '5',
                ],
            ]
        );
    }

    public function testRefundSuccess()
    {
        $this->getMockRequest()
            ->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev')
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => '2a9N3aJuXItx/+OtgycJAcuibhDAEOdsKFPqhkKKO8Y=',
                    'Ds_MerchantCode' => '999008881',
                    'Ds_Terminal' => '871',
                    'Ds_Response' => '0900',
                    'Ds_AuthorisationCode' => '999999',
                    'Ds_TransactionType' => '3',
                    'Ds_SecurePayment' => '2',
                    'Ds_Language' => '2',
                    'Ds_Card_Country' => '724',
                    'Ds_Card_Brand' => '1',
                    'Ds_ProcessedPayMethod' => '5',
                ],
            ]
        );

        $this->assertFalse($this->response->isCancelled());
        $this->assertFalse($this->response->isPending());
        $this->assertFalse($this->response->isRedirect());
        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isTransparentRedirect());

        $this->assertSame('0900', $this->response->getCode());
        $this->assertSame('999999', $this->response->getTransactionReference());
        $this->assertSame(null, $this->response->getMerchantData());
        $this->assertSame('724', $this->response->getCardCountry());
    }

    public function testRefundSuccessUpperResponse()
    {
        $this->getMockRequest()
            ->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev')
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'DS_AMOUNT' => '145',
                    'DS_CURRENCY' => '978',
                    'DS_ORDER' => '0123abc',
                    'DS_SIGNATURE' => '2a9N3aJuXItx/+OtgycJAcuibhDAEOdsKFPqhkKKO8Y=',
                    'DS_MERCHANTCODE' => '999008881',
                    'DS_TERMINAL' => '871',
                    'DS_RESPONSE' => '0900',
                    'DS_AUTHORISATIONCODE' => '999999',
                    'DS_TRANSACTIONTYPE' => '3',
                    'DS_SECUREPAYMENT' => '2',
                    'DS_LANGUAGE' => '2',
                    'DS_CARD_COUNTRY' => '724',
                    'DS_CARD_BRAND' => '1',
                    'DS_PROCESSEDPAYMETHOD' => '5',
                ],
            ]
        );

        $this->assertFalse($this->response->isCancelled());
        $this->assertFalse($this->response->isPending());
        $this->assertFalse($this->response->isRedirect());
        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isTransparentRedirect());

        $this->assertSame('0900', $this->response->getCode());
    }

    public function testRefundSuccessCreditCardSignature()
    {
        $this->getMockRequest()
            ->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev')
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => 'N3g3mJmgIWpvfKVCdH3RjnVnZU+mFuvRxYV447N26h0=',
                    'Ds_MerchantCode' => '999008881',
                    'Ds_Terminal' => '871',
                    'Ds_Response' => '0900',
                    'Ds_AuthorisationCode' => '999999',
                    'Ds_TransactionType' => '3',
                    'Ds_SecurePayment' => '2',
                    'Ds_Language' => '2',
                    'Ds_CardNumber' => '123456********12',
                    'Ds_Card_Country' => '724',
                    'Ds_Card_Brand' => '1',
                    'Ds_ProcessedPayMethod' => '5',
                ],
            ]
        );

        $this->assertFalse($this->response->isCancelled());
        $this->assertFalse($this->response->isPending());
        $this->assertFalse($this->response->isRedirect());
        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isTransparentRedirect());

        $this->assertSame('0900', $this->response->getCode());
    }

    public function testGetMessage()
    {
        $this->getMockRequest()
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => 'SIS0057',
            ]
        );

        $this->response->getMessage();
    }

    public function testRefundFailureAmountExceeded()
    {
        $this->getMockRequest()
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => 'SIS0057',
            ]
        );
        $this->assertEquals('SIS0057', $this->response->getCode());

        $this->assertFalse($this->response->isCancelled());
        $this->assertFalse($this->response->isPending());
        $this->assertFalse($this->response->isRedirect());
        $this->assertFalse($this->response->isSuccessful());
        $this->assertFalse($this->response->isTransparentRedirect());
    }

    public function testRefundFailureTooManyRequests()
    {
        $this->expectException(\Omnipay\Common\Http\Exception\RequestException::class);
        $this->expectExceptionMessage('Too many requests. "SIS0295"');

        $this->getMockRequest()
            ->shouldReceive('getParameters')->once()->andReturn([])
            ->shouldReceive('getEndpoint')->once()->andReturn('https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada');

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => 'SIS0295',
            ]
        );
        $this->assertEquals('SIS0057', $this->response->getCode());

        $this->assertFalse($this->response->isCancelled());
        $this->assertFalse($this->response->isPending());
        $this->assertFalse($this->response->isRedirect());
        $this->assertFalse($this->response->isSuccessful());
        $this->assertFalse($this->response->isTransparentRedirect());
    }
}
