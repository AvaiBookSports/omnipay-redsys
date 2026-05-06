<?php

namespace Omnipay\Redsys\Message;

use Mockery as m;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Tests\TestCase;

class WebservicePurchaseResponseTest extends TestCase
{
    private $mockWsRequest;

    #[\Override]
    public function getMockRequest()
    {
        if (null === $this->mockWsRequest) {
            $this->mockWsRequest = m::mock(WebservicePurchaseRequest::class);
        }

        return $this->mockWsRequest;
    }

    public function testPurchaseSuccess(): void
    {
        $this->getMockRequest()
            ->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev')
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $response = new WebservicePurchaseResponse(
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
                    'Ds_Response' => '0000',
                    'Ds_AuthorisationCode' => '999999',
                    'Ds_TransactionType' => 'A',
                    'Ds_SecurePayment' => '0',
                    'Ds_Language' => '2',
                    'Ds_MerchantData' => 'Ref: 99zz',
                    'Ds_Card_Country' => '724',
                ],
            ]
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('999999', $response->getTransactionReference());
        $this->assertSame(0, (int) $response->getCode());
    }

    public function testPurchaseSuccessUpperResponse(): void
    {
        $this->getMockRequest()
            ->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev')
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'DS_AMOUNT' => '145',
                    'DS_CURRENCY' => '978',
                    'DS_ORDER' => '0123abc',
                    'DS_SIGNATURE' => 'mQF1RU05OZAKwkn7XWDFayiJwWZAI6MxUqyyR50HkPQ=',
                    'DS_MERCHANTCODE' => '999008881',
                    'DS_TERMINAL' => '871',
                    'DS_RESPONSE' => '0000',
                    'DS_AUTHORISATIONCODE' => '999999',
                    'DS_TRANSACTIONTYPE' => 'A',
                    'DS_SECUREPAYMENT' => '0',
                    'DS_LANGUAGE' => '2',
                    'DS_MERCHANTDATA' => 'Ref: 99zz',
                    'DS_CARD_COUNTRY' => '724',
                ],
            ]
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('999999', $response->getTransactionReference());
        $this->assertSame(0, (int) $response->getCode());
        $this->assertSame('Ref: 99zz', $response->getMerchantData());
        $this->assertSame(724, (int) $response->getCardCountry());
    }

    public function testPurchaseFailure(): void
    {
        $this->getMockRequest()
            ->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev')
            ->shouldReceive('getParameters')->once()->andReturn([]);

        $response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => 'xyBfo3NLgmsDDXaUjTkBmM8vOD8X/jrNDaBAAN2qMyE=',
                    'Ds_MerchantCode' => '999008881',
                    'Ds_Terminal' => '871',
                    'Ds_Response' => '0180',
                    'Ds_AuthorisationCode' => '++++++',
                    'Ds_TransactionType' => 'A',
                    'Ds_SecurePayment' => '0',
                    'Ds_Language' => '2',
                    'Ds_MerchantData' => 'Ref: 99zz',
                    'Ds_Card_Country' => '0',
                ],
            ]
        );
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(180, (int) $response->getCode());
    }

    public function testPurchaseInvalidNoReturnCode(): void
    {
        $this->getMockRequest()->expects('getParameters')->andReturns([]);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid response from payment gateway (no data)');
        new WebservicePurchaseResponse(
            $this->getMockRequest(),
            [
                'OPERACION' => [],
            ]
        );
    }

    public function testPurchaseInvalidNoTransactionData(): void
    {
        $this->getMockRequest()->expects('getParameters')->andReturns([]);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid response from payment gateway (no data)');
        new WebservicePurchaseResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
            ]
        );
    }

    public function testPurchaseIntegrationError(): void
    {
        $this->getMockRequest()->expects('getParameters')->andReturns([]);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid response from payment gateway: "SIS0042');
        new WebservicePurchaseResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => 'SIS0042',
            ]
        );
    }

    public function testCompletePurchaseInvalidNoOrder(): void
    {
        $this->getMockRequest()->expects('getParameters')->andReturns([]);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid response from payment gateway');
        new WebservicePurchaseResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'Ds_Amount' => '145',
                ],
            ]
        );
    }

    public function testCompletePurchaseInvalidMissingData(): void
    {
        $this->getMockRequest()->expects('getParameters')->andReturns([]);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid response from payment gateway (missing data)');
        new WebservicePurchaseResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'Ds_Amount' => '145',
                    'Ds_Order' => '0123abc',
                ],
            ]
        );
    }

    public function testPurchaseBadSignature(): void
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid response from payment gateway (signature mismatch)');

        $this->getMockRequest()->expects('getHmacKey')->andReturns('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');
        $this->getMockRequest()->expects('getParameters')->once()->andReturn([]);

        new WebservicePurchaseResponse(
            $this->getMockRequest(),
            [
                'CODIGO' => '0',
                'OPERACION' => [
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => '',
                    'Ds_MerchantCode' => '999008881',
                    'Ds_Terminal' => '871',
                    'Ds_Response' => '0000',
                    'Ds_AuthorisationCode' => '999999',
                    'Ds_TransactionType' => 'A',
                    'Ds_SecurePayment' => '0',
                    'Ds_Language' => '2',
                    'Ds_MerchantData' => 'Ref: 99zz',
                    'Ds_Card_Country' => '724',
                ],
            ]
        );
    }
}
