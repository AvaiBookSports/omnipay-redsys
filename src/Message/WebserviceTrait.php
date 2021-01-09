<?php

namespace Omnipay\Redsys\Message;

trait WebserviceTrait
{
    /** @var string */
    protected $liveEndpoint = 'https://sis.redsys.es/sis/services/SerClsWSEntrada';

    /** @var string */
    protected $testEndpoint = 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada';

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
