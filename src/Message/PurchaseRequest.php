<?php

namespace Omnipay\Redsys\Message;

/**
 * Redsys Purchase Request.
 */
class PurchaseRequest extends AbstractRequest
{
    use RedirectionTrait;

    public function getData()
    {
        $this->validate('merchantId', 'terminalId', 'amount', 'currency');

        return [
            // mandatory fields
            'Ds_Merchant_MerchantCode' => $this->getMerchantId(),
            'Ds_Merchant_Terminal' => $this->getTerminalId(),
            'Ds_Merchant_TransactionType' => '0',                          // Authorisation
            'Ds_Merchant_Amount' => $this->getAmountInteger(),
            'Ds_Merchant_Currency' => $this->getCurrencyNumeric(),  // uses ISO-4217 codes
            'Ds_Merchant_Order' => $this->getTransactionId(),
            'Ds_Merchant_MerchantUrl' => $this->getNotifyUrl(),
            // optional fields
            'Ds_Merchant_ProductDescription' => $this->getDescription(),
            'Ds_Merchant_MerchantDescriptor' => mb_substr($this->getDescription(), 0, 25),
            'Ds_Merchant_Cardholder' => $this->getCardholder(),
            'Ds_Merchant_UrlOK' => $this->getReturnUrl(),
            'Ds_Merchant_UrlKO' => $this->getCancelUrl() ?: $this->getReturnUrl(),
            'Ds_Merchant_MerchantName' => $this->getMerchantName(),
            'Ds_Merchant_ConsumerLanguage' => $this->getConsumerLanguage(),
            'Ds_Merchant_MerchantData' => $this->getMerchantData(),
        ];
    }

    public function sendData($data)
    {
        // Avoid sending null parameters, as Redsys will read the keyword null as a string "null"
        foreach ($data as $dataKey => $dataValue) {
            if (null === $dataValue) {
                unset($data[$dataKey]);
            }
        }

        $security = new Security();

        $encoded_data = $security->encodeMerchantParameters($data);

        $response_data = [
            'Ds_SignatureVersion' => Security::VERSION,
            'Ds_MerchantParameters' => $encoded_data,
            'Ds_Signature' => $security->createSignature(
                $encoded_data,
                $data['Ds_Merchant_Order'],
                $this->getHmacKey()
            ),
        ];

        return $this->response = new PurchaseResponse($this, $response_data);
    }
}
