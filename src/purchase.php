<?php

namespace Yeganehha\EcdIpg;


use GuzzleHttp\Client;

class purchase extends Ecd
{
    protected $callbackUrl ;
    protected $buyId ;
    protected $buyNumber ;
    protected $nationalCode ;
    protected $mobile ;
    protected $additionalData = array();
    private $res ;


    /**
     * Generate new transaction for gateway
     * @param int|null $amount
     * @param int|null $buyId
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateTransaction($amount = null , $buyId = null) {
        if ( $amount != null )
            $this->setAmount($amount);
        if ( $buyId != null )
            $this->setBuyId($buyId);

        $data = array(
            "TerminalNumber" => $this->terminal_number,
            "BuyID" => $this->buyId,
            "Amount" => $this->amount,
            "date" => $this->date,
            "time" => $this->time,
            "RedirectURL" => $this->callbackUrl,
            "Language" => $this->language,
            "CheckSum" => $this->getCheckSum(),
        );
        if ( $this->buyNumber )
            $data["BuyNumber"] = $this->buyNumber;
        if ( $this->nationalCode )
            $data["NationalCode"] = $this->nationalCode;
        if ( $this->mobile )
            $data["Mobile"] = $this->mobile;
        if ( $this->additionalData )
            $data["AdditionalData"] = json_encode($this->additionalData);

        $result = $this->call('PayRequest' ,  $data );
        $this->errorCode = $result->ErrorCode;
        $this->errorDescription = $result->ErrorDescription;
        $this->res = $result->Res;
        if ( $result->State === "1" or $result->State === 1 ){
            $this->status = true;
            return $this;
        } else {
            $this->status = false;
            $this->errorCode = $result->ErrorCode;
            $this->errorDescription = $result->ErrorDescription;
            $this->res = $result->Res;
            throw new \Exception($result->ErrorDescription , $result->ErrorCode);
        }
    }


    /**
     * redirect user to gateway.
     * @return $this|void
     */
    public function pay()
    {
        if ( $this->status  === true ) {
            $html = '
            <script type="text/javascript">
                window.addEventListener("load", function(){
                    document.getElementById(\'form\').submit();
                });
            </script>
            <form action="https://ecd.shaparak.ir/ipg_ecd/PayStart" method="POST" id="form">
                <input name="Token" type="hidden" value="' . $this->res . '"/>
            </form>
        ';
            echo $html;
            exit;
        }
        return $this;
    }

    /**
     * set callback url
     * @param string $callbackUrl
     * @return $this
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }


    /**
     * set buy Id
     * @param string $buyId
     * @return $this
     */
    public function setBuyId($buyId)
    {
        $this->buyId = $buyId;
        return $this;
    }


    /**
     * set payer nationalCode ( optional )
     * @param string $nationalCode
     * @return $this
     */
    public function setNationalCode($nationalCode)
    {
        $this->nationalCode = $nationalCode;
        return $this;
    }


    /**
     * set payer mobile number ( optional )
     * @param string $mobile
     * @return $this
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }


    /**
     * add additional data ( optional )
     * @param string|array $variable
     * @param mixed|null $value
     * @return $this
     */
    public function addAdditionalData($variable , $value = null )
    {
        if ( is_array($variable) )
            $this->additionalData = array_merge($this->additionalData  , $variable) ;
        else {
            if ( $value == null )
                $this->additionalData[] = $variable;
            else
                $this->additionalData[$variable] = $value;
        }
        return $this;
    }

    /**
     * set additional data ( optional )
     * @param mixed $additionalData
     * @return $this
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = (array) $additionalData;
        return $this;
    }

    /**
     * add buy number ( optional )
     * @param mixed $buyNumber
     */
    public function setBuyNumber($buyNumber)
    {
        $this->buyNumber = $buyNumber;
        return $this;
    }

    private function getCheckSum()
    {
        $params_string =
            $this->terminal_number.
            $this->buyId .
            $this->amount .
            $this->date .
            $this->time .
            $this->callbackUrl .
            $this->key;

        return sha1($params_string);
    }

    /**
     * get transaction code
     * @return string
     */
    public function getRes()
    {
        return $this->res;
    }

    /**
     * get transaction code
     * @return string
     */
    public function getTransactionCode()
    {
        return $this->res;
    }

    /**
     * get status of generation
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }
}