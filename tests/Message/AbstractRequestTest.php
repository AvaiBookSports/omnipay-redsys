<?php

namespace Omnipay\Redsys\Message;

use AvaiBookSports\Component\RedsysMessages\CatalogInterface;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    /**
     * @var AbstractRequest
     */
    private $abstractRequest;

    public function setUp(): void
    {
        $this->abstractRequest = $this->getMockForAbstractClass(AbstractRequest::class, [$this->getHttpClient(), $this->getHttpRequest()]);
    }

    public function testGetConsumerLanguage()
    {
        $this->abstractRequest->setLanguage('es');
        $this->assertSame('001', $this->abstractRequest->getConsumerLanguage());

        $this->abstractRequest->setLanguage('en');
        $this->assertSame('002', $this->abstractRequest->getConsumerLanguage());

        $this->abstractRequest->setLanguage('fr');
        $this->assertSame('004', $this->abstractRequest->getConsumerLanguage());

        $this->abstractRequest->setLanguage(null);
        $this->assertSame('002', $this->abstractRequest->getConsumerLanguage());

        $this->abstractRequest->setLanguage('foo');
        $this->assertSame('002', $this->abstractRequest->getConsumerLanguage());
    }

    public function testGetTransactionId()
    {
        $this->abstractRequest->setTransactionId('0abc124');
        $this->assertSame('0abc124', $this->abstractRequest->getTransactionId());

        $this->abstractRequest->setTransactionId('0123456789ab');
        $this->assertSame('0123456789ab', $this->abstractRequest->getTransactionId());
    }

    public function testGetTransactionIdException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"transactionId" has a maximum length of 12 characters');
        $this->abstractRequest->setTransactionId('0123456789abc');
    }

    public function testGetMessageCatalog()
    {
        $this->assertSame(null, $this->abstractRequest->getLanguage());
        $this->assertInstanceOf(CatalogInterface::class, $this->abstractRequest->getMessageCatalog());

        $this->abstractRequest->setLanguage('fr');
        $this->assertSame('fr', $this->abstractRequest->getLanguage());
        $this->assertInstanceOf(CatalogInterface::class, $this->abstractRequest->getMessageCatalog());
    }
}
