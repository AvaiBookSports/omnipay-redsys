<?php

declare(strict_types=1);

namespace Omnipay\Redsys\Message;

use SimpleXMLElement;

class CompleteAuthorizeRequest extends PurchaseRequest
{
    public function getData()
    {
        return array_merge($this->httpRequest->query->all(), $this->httpRequest->request->all());
    }

    public function sendData($data)
    {
        return $this->response = new CompleteAuthorizeResponse($this, $data);
    }
}
