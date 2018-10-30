<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 31.10.18
 */

namespace xXc\SmsGatewaySender\Request\Message;


use xXc\SmsGatewaySender\Request\Validator;

class CyrillicMessage implements Validator
{
    protected $messageLength = 160;

    public function validate(&$data)
    {
        // TODO: Implement validate() method.
    }

    public function clean($data)
    {
        // TODO: Implement clean() method.
    }

    private function transliterate($text)
    {
        // TODO: Implement transliterate() method.
    }
}