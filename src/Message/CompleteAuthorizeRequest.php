<?php

declare(strict_types=1);

namespace Omnipay\Redsys\Message;

class CompleteAuthorizeRequest extends PurchaseRequest
{
    #[\Override]
    public function getData(): array
    {
        return array_merge($this->httpRequest->query->all(), $this->httpRequest->request->all());
    }

    #[\Override]
    public function sendData($data)
    {
        return $this->response = new CompleteAuthorizeResponse($this, $data);
    }
}
