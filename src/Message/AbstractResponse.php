<?php

declare(strict_types=1);

namespace Omnipay\Redsys\Message;

abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{
    /**
     * Helper method to get a specific response parameter if available.
     *
     * @param string $key The key to look up
     *
     * @return mixed|null
     */
    abstract protected function getKey($key);

    /**
     * @return string|null
     */
    protected function getAuthorisationCode()
    {
        $value = $this->getKey('Ds_AuthorisationCode');

        $value = str_replace('+', '', $value);
        $value = trim($value);

        if ('' === $value) {
            return null;
        }

        return $value;
    }
}
