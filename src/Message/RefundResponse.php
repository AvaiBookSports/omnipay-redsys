<?php

namespace Omnipay\Redsys\Message;

use AvaiBookSports\Component\RedsysMessages\Exception\CatalogNotFoundException;
use AvaiBookSports\Component\RedsysMessages\Factory;
use AvaiBookSports\Component\RedsysMessages\Loader\CatalogLoader;
use Http\Discovery\MessageFactoryDiscovery;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Http\Exception\RequestException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Redsys Purchase Response.
 */
class RefundResponse extends AbstractResponse
{
    /** @var string */
    protected $returnSignature;

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
     * @throws InvalidResponseException If resopnse format is incorrect, data is missing, or signature does not match
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

        if (!isset($data['CODIGO'])) {
            throw new InvalidResponseException('Invalid response from payment gateway (no data)');
        }

        if (!isset($data['OPERACION'])) {
            if ('0' == $data['CODIGO']) {
                throw new InvalidResponseException('Invalid response from payment gateway (no data)');
            }
        }

        // Exceeder API rate limit
        if ('SIS0295' == $data['CODIGO'] || '9295' == $data['CODIGO']) {
            throw new RequestException('Too many requests. "'.$data['CODIGO'].'"', MessageFactoryDiscovery::find()->createRequest('POST', $this->getRequest()->getEndpoint(), ['SOAPAction' => 'trataPeticion']));
        }

        if (isset($data['OPERACION']['DS_ORDER'])) {
            $this->usingUpcaseResponse = true;
        }

        if (!empty($data['OPERACION'])) {
            if (!empty($data['OPERACION']['Ds_CardNumber'])) {
                $signature_keys = [
                    'Ds_Amount',
                    'Ds_Order',
                    'Ds_MerchantCode',
                    'Ds_Currency',
                    'Ds_Response',
                    'Ds_CardNumber',
                    'Ds_TransactionType',
                    'Ds_SecurePayment',
                ];
            } else {
                $signature_keys = [
                    'Ds_Amount',
                    'Ds_Order',
                    'Ds_MerchantCode',
                    'Ds_Currency',
                    'Ds_Response',
                    'Ds_TransactionType',
                    'Ds_SecurePayment',
                ];
            }

            $signature_data = '';
            foreach ($signature_keys as $key) {
                $value = $this->getKey($key);
                if (null === $value) {
                    throw new InvalidResponseException('Invalid response from payment gateway (missing data)');
                }
                $signature_data .= $value;
            }

            $this->returnSignature = $security->createSignature(
                $signature_data,
                $this->getKey('Ds_Order'),
                $this->request->getHmacKey()
            );

            if ($this->returnSignature != $this->getKey('Ds_Signature')) {
                throw new InvalidResponseException('Invalid response from payment gateway (signature mismatch)');
            }
        }
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        $response_code = $this->getKey('Ds_Response');

        // check for field existence as well as value
        return isset($this->data['CODIGO'])
            && '0' == $this->data['CODIGO']
            && null !== $response_code
            && is_numeric($response_code)
            && 900 == $response_code;
    }

    /**
     * Helper method to get a specific response parameter if available.
     *
     * @param string $key The key to look up
     *
     * @return mixed|null
     */
    protected function getKey($key)
    {
        if ($this->usingUpcaseResponse) {
            $key = strtoupper($key);
        }

        return isset($this->data['OPERACION'][$key]) ? $this->data['OPERACION'][$key] : null;
    }

    /**
     * Get the authorisation code if available.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        return $this->getKey('Ds_AuthorisationCode');
    }

    /**
     * Get the merchant message if available.
     *
     * @return string|null A response message from the payment gateway
     */
    public function getMessage()
    {
        $message = $this->redsysMessages->getDsResponseMessage($this->getCode());

        if (null === $message) {
            $message = $this->redsysMessages->getErrorMessage($this->getCode());
        }

        return $message;
    }

    /**
     * Get the merchant response code if available.
     *
     * @return string|null
     */
    public function getCode()
    {
        $code = $this->getKey('Ds_Response');

        if (null === $code) {
            $code = $this->data['CODIGO'];
        }

        return $code;
    }

    /**
     * Get the merchant data if available.
     *
     * @return string|null
     */
    public function getMerchantData()
    {
        return $this->getKey('Ds_MerchantData');
    }

    /**
     * Get the card country if available.
     *
     * @return string|null ISO 3166-1 (3-digit numeric) format, if supplied
     */
    public function getCardCountry()
    {
        return $this->getKey('Ds_Card_Country');
    }
}
