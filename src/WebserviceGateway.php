<?php

namespace Omnipay\Redsys;

/**
 * Redsys Webservice Gateway.
 *
 * @see http://www.redsys.es/
 */
class WebserviceGateway extends RedirectGateway
{
    public function getName()
    {
        return 'Redsys Webservice';
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest(\Omnipay\Redsys\Message\WebservicePurchaseRequest::class, $parameters);
    }
}
