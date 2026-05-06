<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Redsys Purchase Response.
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    private readonly string $redirectUrl;

    public function __construct(AbstractRequest $request, mixed $data)
    {
        parent::__construct($request, $data);
        $this->redirectUrl = $request->getEndpoint();
    }

    public function isSuccessful()
    {
        return false;
    }

    #[\Override]
    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    #[\Override]
    public function getRedirectMethod()
    {
        return 'POST';
    }

    #[\Override]
    public function getRedirectData()
    {
        return $this->data;
    }
}
