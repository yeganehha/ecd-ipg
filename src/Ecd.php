<?php

namespace Yeganehha\EcdIpg;

abstract class Ecd
{
    protected $key;
    protected $terminal_number;

    public function __construct($terminal_number = null , $key = null)
    {
        if ( $terminal_number )
            $this->setTerminalNumber($terminal_number);
        if ( $key )
            $this->setKey($key);
    }

    public static function instance($terminal_number = null , $key = null)
    {
        return new static($terminal_number , $key);
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
}