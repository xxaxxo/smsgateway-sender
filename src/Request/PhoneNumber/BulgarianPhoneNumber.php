<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 30.10.18
 */

namespace xXc\SmsGatewaySender\Request\PhoneNumber;

use xXc\SmsGatewaySender\Request\Validator;

class BulgarianPhoneNumber implements Validator
{
    protected $countryCode = 359;
    protected $phoneLength = 12;

    public function validate(&$number)
    {
        $number = $this->clean($number);

        if(!preg_match('{^359[\d]+$}', $number))
        {
            return false;
        }

        if(strlen($number) != $this->phoneLength)
        {
            return false;
        }

        return true;
    }

    public function clean($number)
    {
        return preg_replace('{\s|\+}','',$number);
    }
}