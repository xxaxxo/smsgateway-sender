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
    protected $phoneLength = 9;

    /**
     * validates and cleans a number (removes spaces and initial +)
     * @param $number
     * @return bool
     */
    public function validate(&$number)
    {
        $number = $this->clean($number);

        if(!preg_match('{^'.$this->countryCode.'[\d]{'.$this->phoneLength.'}$}', $number))
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