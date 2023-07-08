<?php

namespace Yeganehha\EcdIpg;

use GuzzleHttp\Exception\GuzzleException;
use Yeganehha\EcdIpg\AbstractClass\Ecd;
use Yeganehha\EcdIpg\Model\Transaction;

class transactions extends Ecd
{
    public $filters = array();

    private function addFilter($type , $value)
    {
        $this->filters[$type] = $value;
    }

    /**
     * Filter By Token
     * @param string $filer
     * @return $this
     */
    public function filterByToken($filer)
    {
        $this->addFilter('Token' , $filer);
        return $this;
    }

    /**
     * Filter By Buy ID
     * @param string $filer
     * @return $this
     */
    public function filterBuyID($filer)
    {
        $this->addFilter('BuyID' , $filer);
        return $this;
    }

    /**
     * Filter By Reference Number
     * @param string $filer
     * @return $this
     */
    public function filterByReferenceNumber($filer)
    {
        $this->addFilter('ReferenceNumber' , $filer);
        return $this;
    }

    /**
     * Filter By Status
     * @param string $filer
     * @return $this
     */
    public function filterByStatus($filer)
    {
        $this->addFilter('Status' , $filer);
        return $this;
    }

    /**
     * Filter By Response Code
     * @param string $filer
     * @return $this
     */
    public function filterByResponseCode($filer)
    {
        $this->addFilter('ResponseCode' , $filer);
        return $this;
    }
    /**
     * Filter By Date ( Default : Today )
     * @param string $filer
     * @return $this
     */
    public function filterByDate($filer)
    {
        $this->addFilter('Date' , $filer);
        return $this;
    }

    /**
     * search transactions by filter inserted
     * @return array
     * @throws \Exception
     */
    public function search()
    {
        $data = $this->filters;
        $data['TerminalNumber'] = $this->terminal_number;
        $data['Key'] = $this->key;
        return $this->get($data);
    }

    /**
     * all transactions of today
     * @return array
     * @throws \Exception
     */
    public function today()
    {
        $data['TerminalNumber'] = $this->terminal_number;
        $data['Key'] = $this->key;
        return $this->get($data);
    }

    /**
     * all transactions of today
     * @return Transaction|null
     * @throws \Exception
     */
    public function getByBuyID($buyID)
    {
        $this->filterBuyID($buyID);
        $data = $this->filters;
        $data['TerminalNumber'] = $this->terminal_number;
        $data['Key'] = $this->key;
        $transactions = $this->get($data);
        if (  is_array($transactions) and count($transactions) == 1 ) {
            return $transactions[0];
        }
        return null;
    }

    /**
     * all transactions of today
     * @return Transaction|null
     * @throws \Exception
     */
    public function getByToken($Token)
    {
        $this->filterByToken($Token);
        $data = $this->filters;
        $data['TerminalNumber'] = $this->terminal_number;
        $data['Key'] = sha1($this->key);
        $transactions = $this->get($data);
        if (  is_array($transactions) and count($transactions) == 1 ) {
            return $transactions[0];
        }
        return null;
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    private function get($data) {
        $result = $this->call('transactions' , $data);
        $this->errorCode = $result->ErrorCode;
        $this->errorDescription = $result->ErrorDescription;
        if ( $result->State === "1" or $result->State === 1 ){
            $items = array();
            foreach ( (array)  $result->Res as $item ){
                $baseOnRial = $this->convertRate == 1 ;
                $items[] = (new Transaction($this->terminal_number  , $this->key  , $baseOnRial ,  $this->language , $this->timeZone ))->setTransaction($item);
            }
            return $items;
        } else {
            $this->errorCode = $result->ErrorCode;
            $this->errorDescription = $result->ErrorDescription;
            throw new \Exception($result->ErrorDescription , $result->ErrorCode);
        }
    }
}