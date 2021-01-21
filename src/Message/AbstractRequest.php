<?php

namespace Omnipay\Redsys\Message;

use AvaiBookSports\Component\RedsysMessages\Exception\CatalogNotFoundException;
use AvaiBookSports\Component\RedsysMessages\Factory;
use AvaiBookSports\Component\RedsysMessages\Loader\CatalogLoader;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Message\AbstractRequest as MessageAbstractRequest;

abstract class AbstractRequest extends MessageAbstractRequest
{
    /** @var array */
    protected static $consumerLanguages = [
        'es' => '001', // Spanish
        'en' => '002', // English
        'ca' => '003', // Catalan - same as Valencian (010)
        'fr' => '004', // French
        'de' => '005', // German
        'nl' => '006', // Dutch
        'it' => '007', // Italian
        'sv' => '008', // Swedish
        'pt' => '009', // Portuguese
        'pl' => '011', // Polish
        'gl' => '012', // Galician
        'eu' => '013', // Basque
        'bg' => '100', // Undocumented
        'zh' => '156', // Undocumented
        'hr' => '191', // Undocumented
        'cs' => '203', // Undocumented
        'da' => '208', // Undocumented
        'et' => '233', // Undocumented
        'fi' => '246', // Undocumented
        'el' => '300', // Undocumented
        'hu' => '348', // Undocumented
        'ja' => '392', // Undocumented
        'lv' => '428', // Undocumented
        'lt' => '440', // Undocumented
        'mt' => '470', // Undocumented
        'ro' => '642', // Undocumented
        'ru' => '643', // Undocumented
        'sk' => '703', // Undocumented
        'sl' => '705', // Undocumented
        'tr' => '792', // Undocumented
    ];

    abstract public function getEndpoint();

    public function getCardholder()
    {
        return $this->getParameter('cardholder');
    }

    public function setCardholder($value)
    {
        return $this->setParameter('cardholder', $value);
    }

    /**
     * Get the language presented to the consumer.
     *
     * @return string|null ISO 639-1 code
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * Set the language presented to the consumer.
     *
     * @param string ISO 639-1 code
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    /**
     * Returns Redsys language code or English by default.
     *
     * @return string
     */
    public function getConsumerLanguage()
    {
        $language = $this->getLanguage() ?: 'en';

        if (!array_key_exists($language, self::$consumerLanguages)) {
            $language = 'en';
            // throw new OmnipayException(sprintf('Language "%s" is not supported by the gateway', $language));
        }

        return self::$consumerLanguages[$language];
    }

    public function getHmacKey()
    {
        return $this->getParameter('hmacKey');
    }

    public function setHmacKey($value)
    {
        return $this->setParameter('hmacKey', $value);
    }

    public function getMerchantData()
    {
        return $this->getParameter('merchantData');
    }

    public function setMerchantData($value)
    {
        return $this->setParameter('merchantData', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantName()
    {
        return $this->getParameter('merchantName');
    }

    public function setMerchantName($value)
    {
        return $this->setParameter('merchantName', $value);
    }

    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    /**
     * Override the abstract method to add requirement that it must start with 4 numeric characters.
     *
     * @param string|int $value The transaction ID (merchant order) to set for the transaction
     */
    public function setTransactionId($value)
    {
        if (strlen($value) > 12) {
            throw new RuntimeException('"transactionId" has a maximum length of 12 characters');
        }

        parent::setTransactionId($value);
    }

    public function getMessageCatalog()
    {
        try {
            return (new Factory(new CatalogLoader()))->createCatalogByLanguage($this->getLanguage() ?: 'en');
        } catch (CatalogNotFoundException $e) {
            return (new Factory(new CatalogLoader()))->createCatalogByLanguage('en');
        }
    }
}
