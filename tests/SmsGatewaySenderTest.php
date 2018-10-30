<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 30.10.18
 */

namespace xXc\SmsGatewaySender;
use PHPUnit_Framework_TestCase;

class SmsGatewaySenderTest extends PHPUnit_Framework_TestCase
{
    public $smsGatewaySender;

    public function setUp()
    {
        parent::setUp();
        $this->smsGatewaySender = new SmsGatewaySender();
    }

    /** @test */
    function a_message_cannot_be_sent_without_data()
    {
        try {
            $this->smsGatewaySender->send();
        } catch(\Exception $exception) {
            return true;
        }
        $this->fail('An exception was not thrown but expected');
    }

}