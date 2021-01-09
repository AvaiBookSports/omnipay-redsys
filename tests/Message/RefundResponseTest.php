<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Tests\TestCase;

class RefundResponseTest extends TestCase
{
    /** @var RefundResponse */
    private $response;

    public function testRefundSuccess()
    {
        $this->getMockRequest()
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => 'mQF1RU05OZAKwkn7XWDFayiJwWZAI6MxUqyyR50HkPQ=',
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
    }

    public function testRefundFailure()
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid response from payment gateway: "SIS0057');

        $this->getMockRequest()
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $this->response = new RefundResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => 'SIS0057',
            ]
        );

        // $this->assertFalse($this->response->isCancelled());
        // $this->assertFalse($this->response->isPending());
        // $this->assertFalse($this->response->isRedirect());
        // $this->assertFalse($this->response->isSuccessful());
        // $this->assertFalse($this->response->isTransparentRedirect());
        // $this->assertSame('SIS0057', $this->response->getCode());
    }
}
