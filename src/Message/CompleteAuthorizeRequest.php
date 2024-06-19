<?php

declare(strict_types=1);

namespace Omnipay\Redsys\Message;

use SimpleXMLElement;

class CompleteAuthorizeRequest extends AbstractRequest
{
    use WebserviceTrait;

    public function getData()
    {
        $this->validate('merchantId', 'terminalId', 'amount', 'currency');

        $data = [
            // mandatory fields
            'DS_MERCHANT_ORDER' => $this->getTransactionId(),
            'DS_MERCHANT_MERCHANTCODE' => (string) $this->getMerchantId(),
            'DS_MERCHANT_TERMINAL' => (string) $this->getTerminalId(),
            'DS_MERCHANT_CURRENCY' => (string) $this->getCurrencyNumeric(),
            'DS_MERCHANT_TRANSACTIONTYPE' => '2',
            'DS_MERCHANT_AMOUNT' => (string) $this->getAmountInteger(),
        ];

        $request = new SimpleXMLElement('<REQUEST/>');
        $requestData = $request->addChild('DATOSENTRADA');
        foreach ($data as $tag => $value) {
            $requestData->addChild($tag, $value);
        }

        $security = new Security();

        $request->addChild('DS_SIGNATUREVERSION', Security::VERSION);
        $request->addChild('DS_SIGNATURE', $security->createSignature(
            $requestData->asXML(),
            $data['DS_MERCHANT_ORDER'],
            $this->getHmacKey()
        ));

        // keep data as nested array for method signature compatibility
        return [
            'DATOSENTRADA' => $data,
            'DS_SIGNATUREVERSION' => (string) $request->DS_SIGNATUREVERSION,
            'DS_SIGNATURE' => (string) $request->DS_SIGNATURE,
        ];
    }

    public function sendData($data)
    {
        // re-create the XML
        $request = new SimpleXMLElement('<REQUEST/>');
        $requestData = $request->addChild('DATOSENTRADA');
        foreach ($data['DATOSENTRADA'] as $tag => $value) {
            $requestData->addChild($tag, $value);
        }
        $request->addChild('DS_SIGNATUREVERSION', $data['DS_SIGNATUREVERSION']);
        $request->addChild('DS_SIGNATURE', $data['DS_SIGNATURE']);

        // wrap in SOAP envelope
        $requestEnvelope = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/'>
              <soapenv:Header/>
              <soapenv:Body>
                <impl:trataPeticion xmlns:impl='http://webservice.sis.sermepa.es'>
                  <impl:datosEntrada>
                    ".htmlspecialchars($request->asXML()).'
                  </impl:datosEntrada>
                </impl:trataPeticion>
              </soapenv:Body>
            </soapenv:Envelope>';

        // send the actual SOAP request
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            ['SOAPAction' => 'trataPeticion'],
            $requestEnvelope
        );

        // unwrap httpResponse into actual data as SimpleXMLElement tree
        $responseEnvelope = simplexml_load_string($httpResponse->getBody()->getContents());
        $responseData = new SimpleXMLElement(htmlspecialchars_decode(
            (string) $responseEnvelope->children('http://schemas.xmlsoap.org/soap/envelope/')
                ->Body->children('http://webservice.sis.sermepa.es')
                ->trataPeticionResponse
                ->trataPeticionReturn
        ));

        // convert to nested arrays (drop the 'true' to use simple objects)
        $responseData = json_decode(json_encode($responseData), true);

        return $this->response = new CompleteAuthorizeResponse($this, $responseData);
    }
}
