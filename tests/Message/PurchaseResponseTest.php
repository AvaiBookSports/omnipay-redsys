<?php

namespace Omnipay\Redsys\Message;

use Mockery as m;
use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    private $mockAbstractRequest;

    #[\Override]
    public function getMockRequest()
    {
        if (null === $this->mockAbstractRequest) {
            $this->mockAbstractRequest = m::mock(AbstractRequest::class);
        }

        return $this->mockAbstractRequest;
    }

    public function testPurchaseSuccess(): void
    {
        $this->getMockRequest()->shouldReceive('getEndpoint')->andReturn('https://sis-t.redsys.es:25443/sis/realizarPago');

        $response = new PurchaseResponse($this->getMockRequest(), [
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
            'Ds_MerchantParameters' => 'eyJEc19NZXJjaGFudF9NZXJjaGFudENvZGUiOiI5OTkwMDg4ODEiLCJEc19NZXJjaGFudF9UZXJtaW5'
                .'hbCI6Ijg3MSIsIkRzX01lcmNoYW50X1RyYW5zYWN0aW9uVHlwZSI6IjAiLCJEc19NZXJjaGFudF9BbW91bnQiOiIxNDUiLCJEc19N'
                .'ZXJjaGFudF9DdXJyZW5jeSI6Ijk3OCIsIkRzX01lcmNoYW50X09yZGVyIjoiMDEyM2FiYyIsIkRzX01lcmNoYW50X01lcmNoYW50V'
                .'XJsIjoiaHR0cHM6XC9cL3d3dy5leGFtcGxlLmNvbVwvbm90aWZ5IiwiRHNfTWVyY2hhbnRfUHJvZHVjdERlc2NyaXB0aW9uIjoiTX'
                .'kgc2FsZXMgaXRlbXMiLCJEc19NZXJjaGFudF9DYXJkaG9sZGVyIjoiSiBTbWl0aCIsIkRzX01lcmNoYW50X1VybE9LIjoiaHR0cHM'
                .'6XC9cL3d3dy5leGFtcGxlLmNvbVwvcmV0dXJuIiwiRHNfTWVyY2hhbnRfVXJsS08iOiJodHRwczpcL1wvd3d3LmV4YW1wbGUuY29t'
                .'XC9yZXR1cm4iLCJEc19NZXJjaGFudF9NZXJjaGFudE5hbWUiOiJNeSBTdG9yZSIsIkRzX01lcmNoYW50X0NvbnN1bWVyTGFuZ3VhZ'
                .'2UiOiIwMDIiLCJEc19NZXJjaGFudF9NZXJjaGFudERhdGEiOiJSZWY6IDk5enoifQ==',
            'Ds_Signature' => 'dEYvw2ti+iUS9+sc1U8klNdLpoFPO08hRRzd9LLmLWs=',
        ]);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('https://sis-t.redsys.es:25443/sis/realizarPago', $response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());
        $this->assertSame(
            [
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => 'eyJEc19NZXJjaGFudF9NZXJjaGFudENvZGUiOiI5OTkwMDg4ODEiLCJEc19NZXJjaGFudF9UZXJ'
                    .'taW5hbCI6Ijg3MSIsIkRzX01lcmNoYW50X1RyYW5zYWN0aW9uVHlwZSI6IjAiLCJEc19NZXJjaGFudF9BbW91bnQiOiIxNDUi'
                    .'LCJEc19NZXJjaGFudF9DdXJyZW5jeSI6Ijk3OCIsIkRzX01lcmNoYW50X09yZGVyIjoiMDEyM2FiYyIsIkRzX01lcmNoYW50X'
                    .'01lcmNoYW50VXJsIjoiaHR0cHM6XC9cL3d3dy5leGFtcGxlLmNvbVwvbm90aWZ5IiwiRHNfTWVyY2hhbnRfUHJvZHVjdERlc2'
                    .'NyaXB0aW9uIjoiTXkgc2FsZXMgaXRlbXMiLCJEc19NZXJjaGFudF9DYXJkaG9sZGVyIjoiSiBTbWl0aCIsIkRzX01lcmNoYW5'
                    .'0X1VybE9LIjoiaHR0cHM6XC9cL3d3dy5leGFtcGxlLmNvbVwvcmV0dXJuIiwiRHNfTWVyY2hhbnRfVXJsS08iOiJodHRwczpc'
                    .'L1wvd3d3LmV4YW1wbGUuY29tXC9yZXR1cm4iLCJEc19NZXJjaGFudF9NZXJjaGFudE5hbWUiOiJNeSBTdG9yZSIsIkRzX01lc'
                    .'mNoYW50X0NvbnN1bWVyTGFuZ3VhZ2UiOiIwMDIiLCJEc19NZXJjaGFudF9NZXJjaGFudERhdGEiOiJSZWY6IDk5enoifQ==',
                'Ds_Signature' => 'dEYvw2ti+iUS9+sc1U8klNdLpoFPO08hRRzd9LLmLWs=',
            ],
            $response->getRedirectData()
        );
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCode());
    }
}
