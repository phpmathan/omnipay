<?php

/*
 * This file is part of the Omnipay package.
 *
 * (c) Adrian Macneil <adrian@adrianmacneil.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omnipay\PaymentExpress\Message;

use SimpleXMLElement;
use Omnipay\Common\Message\AbstractRequest;

/**
 * PaymentExpress PxPay Authorize Request
 */
class PxPayAuthorizeRequest extends AbstractRequest
{
    protected $endpoint = 'https://sec.paymentexpress.com/pxpay/pxaccess.aspx';
    protected $action = 'Auth';

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'returnUrl');

        $data = new SimpleXMLElement('<GenerateRequest/>');
        $data->PxPayUserId = $this->getUsername();
        $data->PxPayKey = $this->getPassword();
        $data->TxnType = $this->action;
        $data->AmountInput = $this->getAmount();
        $data->CurrencyInput = $this->getCurrency();
        $data->MerchantReference = $this->getDescription();
        $data->UrlSuccess = $this->getReturnUrl();
        $data->UrlFail = $this->getReturnUrl();

        return $data;
    }

    public function send(array $datas = array(), $doMerge = true)
    {
        $datas = $this->getData();

        $httpResponse = $this->httpClient->post($this->endpoint, null, $datas->asXML())->send();

        return $this->createResponse($httpResponse->xml());
    }

    protected function createResponse($data)
    {
        return $this->response = new PxPayAuthorizeResponse($this, $data);
    }
}
