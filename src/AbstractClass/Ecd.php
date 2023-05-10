<?php

namespace Yeganehha\EcdIpg\AbstractClass;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class Ecd
{
    protected $key;
    protected $terminal_number;
    protected $language  ;
    protected $date  ;
    protected $time  ;

    protected $amount ;
    protected $convertRate ;

    private $timeOut = 5 ;
    private $URI = 'https://ecd.shaparak.ir/ipg_ecd/';


    protected $status;
    protected $errorCode;
    protected $errorDescription;
    protected $timeZone;


    /**
     * @param string|int $terminal_number
     * @param string $key
     * @param boolean $baseOnRial
     * @param string $language
     * @param string $timeZone
     * @throws \Exception
     */
    public function __construct($terminal_number = null , $key = null , $baseOnRial = true,  $language = "fa" , $timeZone = "Asia/Tehran")
    {
        if ( $terminal_number )
            $this->setTerminalNumber($terminal_number);
        if ( $key )
            $this->setKey($key);
        if ( $language == "fa" )
            $this->persian();
        else
            $this->english();

        $this->timeZone = $timeZone;
        $date = new \DateTime("now", new \DateTimeZone($timeZone));
        $this->time = $date->format("h:i");
        $this->date = $date->format("Y-m-d");

        if ( $baseOnRial )
            $this->baseOnRial();
        else
            $this->baseOnToman();
    }

    /**
     * Generate instance of class for call statically
     * @param string|int $terminal_number
     * @param string $key
     * @param boolean $baseOnRial
     * @param string $language
     * @param string $timeZone
     * @return $this
     * @throws \Exception
     */
    public static function instance($terminal_number = null , $key = null , $baseOnRial = true, $language = "fa" , $timeZone = "Asia/Tehran")
    {
        return new static($terminal_number  , $key  , $baseOnRial ,  $language , $timeZone );
    }
    /**
     * Key get from IPG
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }


    /**
     * Terminal number get from IPG
     * @param string|int $terminal_number
     * @return $this
     */
    public function setTerminalNumber($terminal_number)
    {
        $this->terminal_number = $terminal_number;
        return $this;
    }

    /**
     * Change language of gateway to english
     * @return $this
     */
    public function english()
    {
        $this->language = "en";
        return $this;
    }

    /**
     * Change language of gateway to persian
     * @return $this
     */
    public function persian()
    {
        $this->language = "fa";
        return $this;
    }


    /**
     * call when your system currency is Rial.
     * @return $this
     */
    public function baseOnRial()
    {
        $this->convertRate = 1 ;
        return $this;
    }

    /**
     * call when your system currency is Toman.
     * @return $this
     */
    public function baseOnToman()
    {
        $this->convertRate = 10 ;
        return $this;
    }


    /**
     * set amount of transaction
     * @param int $amount
     * @param boolean|null $baseOnRial
     * @return $this
     */
    public function setAmount($amount, $baseOnRial = null)
    {

        if ( $baseOnRial === true)
            $this->baseOnRial();
        elseif ( $baseOnRial === false )
            $this->baseOnToman();

        $this->amount = $amount  * $this->convertRate;
        return $this;
    }


    /**
     * Set Time out for all request to gateway
     * @param int $timeOut
     * @return $this
     */
    public function setTimeOut($timeOut)
    {
        $this->timeOut = (int) $timeOut;
        return $this;
    }

    /**
     * @param $scope
     * @param $data
     * @param $isPost
     * @return mixed
     * @throws GuzzleException
     */
    protected function call($scope  , $data  = null , $isPost = true)
    {
        $client = new Client(['base_uri' => trim($this->URI , '/') . '/','timeout'  => $this->timeOut]);
        if ( $isPost === true)
            $response = $client->request('POST' , $scope , array('form_params' => $data));
        elseif ( $isPost === false)
            $response = $client->request('GET' , $scope , array('query' => $data));
        else
            $response = $client->request(strtoupper($isPost) , $scope ,  $data);

        $result = $response->getBody()->getContents();
        return json_decode($result);
    }


    /**
     * get Error Code
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get error message
     * @return string|null
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }
}