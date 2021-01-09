<?php

namespace Omnipay\Redsys;

use Omnipay\Common\AbstractGateway;

/**
 * Redsys Redirect Gateway.
 *
 * @see http://www.redsys.es/
 */
class RedirectGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Redsys Redirect';
    }

    public function getDefaultParameters()
    {
        return [
            'merchantId' => '',
            'merchantName' => '',
            'terminalId' => '',
            'hmacKey' => '',
            'testMode' => false,
        ];
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantName()
    {
        return $this->getParameter('merchantName');
    }

    public function setMerchantName($value)
    {
        return $this->setParameter('merchantName', $value);
    }

    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    public function getHmacKey()
    {
        return $this->getParameter('hmacKey');
    }

    public function setHmacKey($value)
    {
        return $this->setParameter('hmacKey', $value);
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest(\Omnipay\Redsys\Message\PurchaseRequest::class, $parameters);
    }

    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(\Omnipay\Redsys\Message\CompletePurchaseRequest::class, $parameters);
    }

    public function refund($parameters = [])
    {
        return $this->createRequest(\Omnipay\Redsys\Message\RefundRequest::class, $parameters);
    }
}
