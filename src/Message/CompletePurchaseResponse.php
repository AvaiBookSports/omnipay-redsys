<?php

namespace Omnipay\Redsys\Message;

use AvaiBookSports\Component\RedsysMessages\CatalogInterface;
use AvaiBookSports\Component\RedsysMessages\Exception\CatalogNotFoundException;
use AvaiBookSports\Component\RedsysMessages\Factory;
use AvaiBookSports\Component\RedsysMessages\Loader\CatalogLoader;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;

/**
 * Redsys Complete Purchase Response.
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /** @var array */
    protected $merchantParameters;

    /** @var string */
    protected $returnSignature;

    /** @var bool */
    protected $usingUpcaseParameters = false;

    /** @var bool */
    protected $usingUpcaseResponse = false;

    /** @var CatalogInterface */
    protected $redsysMessages;

    /**
     * Constructor.
     *
     * @param RequestInterface $request the initiating request
     * @param mixed            $data
     *
     * @throws InvalidResponseException If merchant data or order number is missing, or signature does not match
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $security = new Security();

        try {
            $this->redsysMessages = (new Factory(new CatalogLoader()))->createCatalogByLanguage(array_key_exists('language', $this->request->getParameters()) ? $this->request->getParameters()['language'] : 'en');
        } catch (CatalogNotFoundException $e) {
            $this->redsysMessages = (new Factory(new CatalogLoader()))->createCatalogByLanguage('en');
        }

        if (!empty($data['Ds_MerchantParameters'])) {
            $this->merchantParameters = $security->decodeMerchantParameters($data['Ds_MerchantParameters']);
        } elseif (!empty($data['DS_MERCHANTPARAMETERS'])) {
            $this->merchantParameters = $security->decodeMerchantParameters($data['DS_MERCHANTPARAMETERS']);
            $this->usingUpcaseResponse = true;
        } else {
            throw new InvalidResponseException('Invalid response from payment gateway (no data)');
        }

        if (!empty($this->merchantParameters['Ds_Order'])) {
            $order = $this->merchantParameters['Ds_Order'];
        } elseif (!empty($this->merchantParameters['DS_ORDER'])) {
            $order = $this->merchantParameters['DS_ORDER'];
            $this->usingUpcaseParameters = true;
        } else {
            throw new InvalidResponseException();
        }

        $this->returnSignature = $security->createReturnSignature(
            $data[$this->usingUpcaseResponse ? 'DS_MERCHANTPARAMETERS' : 'Ds_MerchantParameters'],
            $order,
            $this->request->getHmacKey()
        );

        if ($this->returnSignature != $data[$this->usingUpcaseResponse ? 'DS_SIGNATURE' : 'Ds_Signature']) {
            throw new InvalidResponseException('Invalid response from payment gateway (signature mismatch)');
        }
    }

    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful()
    {
        $key = $this->usingUpcaseParameters ? 'DS_RESPONSE' : 'Ds_Response';

        return isset($this->merchantParameters[$key])
            && is_numeric($this->merchantParameters[$key])
            && 0 <= $this->merchantParameters[$key]
            && 100 > $this->merchantParameters[$key];
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return bool
     */
    public function isCancelled()
    {
        return '9915' === $this->getCode();
    }

    /**
     * Get the response data, included the decoded merchant parameters if available.
     *
     * @return mixed
     */
    public function getData()
    {
        $data = parent::getData();

        return is_array($data) && is_array($this->merchantParameters)
            ? array_merge($data, $this->merchantParameters)
            : $data;
    }

    /**
     * Helper method to get a specific merchant parameter if available.
     *
     * @param string $key The key to look up
     *
     * @return mixed|null
     */
    protected function getKey($key)
    {
        if ($this->usingUpcaseParameters) {
            $key = strtoupper($key);
        }

        return isset($this->merchantParameters[$key]) ? $this->merchantParameters[$key] : null;
    }

    /**
     * Get the authorisation code if available.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        return $this->getAuthorisationCode();
    }

    /**
     * Get the merchant message if available.
     *
     * @return string|null A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->redsysMessages->getDsResponseMessage($this->getCode());
    }

    /**
     * Get the merchant response code if available.
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getKey('Ds_Response');
    }

    /**
     * Get the card type if available.
     *
     * @return string|null
     */
    public function getCardType()
    {
        return $this->getKey('Ds_Card_Type');
    }
}
