<?php

namespace Omnipay\Redsys\Message;

/**
 * Redsys Complete Purchase Request.
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    #[\Override]
    public function getData()
    {
        return array_merge($this->httpRequest->query->all(), $this->httpRequest->request->all());
    }

    #[\Override]
    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
