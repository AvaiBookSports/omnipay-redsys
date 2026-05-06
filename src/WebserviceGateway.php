<?php

namespace Omnipay\Redsys;

/**
 * Redsys Webservice Gateway.
 *
 * @see http://www.redsys.es/
 */
class WebserviceGateway extends RedirectGateway
{
    #[\Override]
    public function getName()
    {
        return 'Redsys Webservice';
    }

    #[\Override]
    public function purchase(array $parameters = [])
    {
        return $this->createRequest(Message\WebservicePurchaseRequest::class, $parameters);
    }

    #[\Override]
    public function refund(array $parameters = [])
    {
        return $this->createRequest(Message\RefundRequest::class, $parameters);
    }
}
