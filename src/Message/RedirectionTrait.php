<?php

namespace Omnipay\Redsys\Message;

trait RedirectionTrait
{
    /** @var string */
    protected $liveEndpoint = 'https://sis.redsys.es/sis/realizarPago';

    /** @var string */
    protected $testEndpoint = 'https://sis-t.redsys.es:25443/sis/realizarPago';

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
