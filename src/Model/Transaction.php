<?php

namespace Yeganehha\EcdIpg\Model;

use GuzzleHttp\Exception\GuzzleException;
use Yeganehha\EcdIpg\AbstractClass\Ecd;

class Transaction extends Ecd
{
    protected $itemInformation ;
    /**
     * Reverse current transaction to payer
     * @return bool
     * @throws GuzzleException
     */
    public function reverse()
    {
        $result = $this->call('PayReverse' , ['Token' => $this->itemInformation->token]);
        if ( $result->State === "1" or $result->State === 1 ){
            return true;
        }
        return false;
    }

    public function setTransaction($item)
    {
        $this->itemInformation = $item;
        return $this;
    }

    /**
     * convert transaction to array
     * @return array
     */
    public function toArray()
    {
        return (array) $this->itemInformation;
    }


    /**
     * get amount
     * @return int
     */
    public function getAmount()
    {
        return $this->itemInformation->Amount;
    }


    /**
     * Is Payment success
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->itemInformation->ResponseCode == "00";
    }


    /**
     * get Reference Number
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->itemInformation->ReferenceNumber;
    }

    /**
     * get Tracking Number
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->itemInformation->TrackingNumber;
    }

    /**
     * get Buy Id
     * @return string
     */
    public function getBuyId()
    {
        return $this->itemInformation->BuyID;
    }

    /**
     * get Token
     * @return string
     */
    public function getToken()
    {
        return $this->itemInformation->Token;
    }

    /**
     * get card Number
     * @return string
     */
    public function getCardNumber()
    {
        $cardNumber = $this->itemInformation->cardNumber ;
        if ( strlen($cardNumber) != 16 )
            $cardNumber = substr($cardNumber , 0 , 6 ) . str_repeat('*' , 6) . substr($cardNumber , -4);
        return $cardNumber;
    }


    /**
     * get Status
     * @return string
     */
    public function getStatus()
    {
        return $this->itemInformation->Status;
    }


    /**
     * get Mobile
     * @return string
     */
    public function getMobile()
    {
        return $this->itemInformation->Mobile;
    }

    /**
     * get NationalCode
     * @return string
     */
    public function getNationalCode()
    {
        return $this->itemInformation->NationalCode;
    }


    /**
     * get Time
     * @return string
     */
    public function getTime()
    {
        return $this->itemInformation->Time;
    }



    /**
     * get Date
     * @return string
     */
    public function getDate()
    {
        return $this->itemInformation->Date;
    }


    /**
     * get Call Back URL
     * @return string
     */
    public function getCallBackURL()
    {
        return $this->itemInformation->CallBackURL;
    }


    /**
     * get AdditionalData
     * @return mixed
     */
    public function getAdditionalData()
    {
        return $this->itemInformation->AdditionalData ? json_decode($this->itemInformation->AdditionalData, true) : array();
    }

}