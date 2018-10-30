<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 30.10.18
 */

namespace xXc\SmsGatewaySender\Request;


interface PhoneNumber
{
    public function validate(&$number);
    public function clean($number);

}