<?php

namespace Yeganehha\EcdIpg;

class TransactionStatus
{
    public static $GET_TOKEN = 1 ;
    public static $PAYER_INSIDE_GATEWAY = 2 ;
    public static $PAID = 210 ;
    public static $CONFIRMED = 220 ;
    public static $UPDATED = 430 ;
}