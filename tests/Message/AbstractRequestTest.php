<?php

namespace Omnipay\Redsys\Message;

use AvaiBookSports\Component\RedsysMessages\CatalogInterface;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractRequestTest extends TestCase
{
    private MockObject&AbstractRequest $abstractRequest;

    #[\Override]
    public function setUp(): void
    {
        $this->abstractRequest = $this->getMockBuilder(AbstractRequest::class)
            ->setConstructorArgs([$this->getHttpClient(), $this->getHttpRequest()])
            ->onlyMethods(['getEndpoint', 'getData', 'sendData'])
            ->getMock();
    }

    public function testGetConsumerLanguage(): void
    {
        $this->abstractRequest->setLanguage('es');
        $this->assertSame('001', $this->abstractRequest->getConsumerLanguage());

        $this->abstractRequest->setLanguage('en');
        $this->assertSame('002', $this->abstractRequest->getConsumerLanguage());

        $this->abstractRequest->setLanguage('fr');
        $this->assertSame('004', $this->abstractRequest->getConsumerLanguage());

        $this->abstractRequest->setLanguage('');
        $this->assertSame('002', $this->abstractRequest->getConsumerLanguage());

        $this->abstractRequest->setLanguage('foo');
        $this->assertSame('002', $this->abstractRequest->getConsumerLanguage());
    }

    public function testGetTransactionId(): void
    {
        $this->abstractRequest->setTransactionId('0abc124');
        $this->assertSame('0abc124', $this->abstractRequest->getTransactionId());

        $this->abstractRequest->setTransactionId('0123456789ab');
        $this->assertSame('0123456789ab', $this->abstractRequest->getTransactionId());
    }

    public function testGetTransactionIdException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"transactionId" has a maximum length of 12 characters');
        $this->abstractRequest->setTransactionId('0123456789abc');
    }

    public function testGetMessageCatalog(): void
    {
        $this->assertSame(null, $this->abstractRequest->getLanguage());
        $this->assertInstanceOf(CatalogInterface::class, $this->abstractRequest->getMessageCatalog());

        $this->abstractRequest->setLanguage('fr');
        $this->assertSame('fr', $this->abstractRequest->getLanguage());
        $this->assertInstanceOf(CatalogInterface::class, $this->abstractRequest->getMessageCatalog());
    }
}
