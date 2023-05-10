<?php

namespace Yeganehha\EcdIpg;

use GuzzleHttp\Exception\GuzzleException;
use Yeganehha\EcdIpg\AbstractClass\Ecd;
use Yeganehha\EcdIpg\Model\Transaction;

class verification extends Ecd
{
    private $reference_number;
    private $tracking_number;
    private $buy_id;
    private $token;
    private $transaction = null;
    private $card_mask = null;
    private $additional_data = null;

    /**
     * check transaction result
     * @return verification
     */
    public function callback()
    {
        if ( $_POST["State"] === "1" or $_POST["State"]  === 1)
            $this->status = true ;
        else
            $this->status = false ;
        $this->amount = $_POST["Amount"];
        $this->errorCode = $_POST["ErrorCode"];
        $this->errorDescription = $_POST["ErrorDescription"];
        $this->reference_number = $_POST["ReferenceNumber"];
        $this->tracking_number = $_POST["TrackingNumber"];
        $this->buy_id = $_POST["BuyID"];
        $this->token = $_POST["Token"];
        return $this;
    }

    /**
     * Confirm callback receive for gateway
     * @return bool
     * @throws GuzzleException
     */
    public function confirm()
    {
        $result = $this->call('PayConfirmation' , ['Token' => $this->token]);
        if ( $result->State === "1" or $result->State === 1 ){
            return true;
        }
        return false;
    }


    /**
     * Reverse current transaction to payer
     * @return bool
     * @throws GuzzleException
     */
    public function reverse()
    {
        $result = $this->call('PayReverse' , ['Token' => $this->token]);
        if ( $result->State === "1" or $result->State === 1 ){
            return true;
        }
        return false;
    }

    /**
     * Is Payment success
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->status;
    }


    /**
     * get Reference Number
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->reference_number;
    }

    /**
     * get Tracking Number
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->tracking_number;
    }

    /**
     * get Buy Id
     * @return mixed
     */
    public function getBuyId()
    {
        return $this->buy_id;
    }

    /**
     * get mask of payer card
     * @return string|null
     * @throws \Exception
     */
    public function getMaskCard()
    {
        if ( $this->card_mask != null )
            return $this->card_mask;
        $this->getTransaction();
        $this->card_mask = $this->transaction->getCardNumber();
        return  $this->card_mask;
    }

    /**
     * get mask of payer card
     * @return string|null
     * @throws \Exception
     */
    public function getAdditionalData()
    {
        if ( $this->additional_data != null )
            return $this->additional_data;
        $this->getTransaction();
        $this->additional_data = $this->transaction->getAdditionalData();
        return  $this->additional_data;
    }

    private function getTransaction()
    {
        if ( $this->transaction === null ) {
            $baseOnRial = $this->convertRate == 1;
            $this->transaction = transactions::instance($this->terminal_number, $this->key, $baseOnRial, $this->language, $this->timeZone)->getByBuyID($this->buy_id);
        }
    }
}