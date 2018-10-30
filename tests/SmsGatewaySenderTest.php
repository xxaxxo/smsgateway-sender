<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 30.10.18
 */

namespace xXc\SmsGatewaySender;
use PHPUnit_Framework_TestCase;
use xXc\SmsGatewaySender\Exception\BadRequestException;

class SmsGatewaySenderTest extends PHPUnit_Framework_TestCase
{
    public $smsGatewaySender;

    public function setUp()
    {
        parent::setUp();
        $this->smsGatewaySender = new SmsGatewaySender();
    }

    /** @test */
    function a_receiver_can_be_set()
    {
        $dummyNumber = '359888888888';
        $this->smsGatewaySender->to($dummyNumber);
        $this->assertSame([$dummyNumber], $this->smsGatewaySender->receiver());
    }

    /** @test */
    function a_multiple_receivers_can_be_set()
    {
        $dummyNumber = ['359888888881', '359888888882'];
        $this->smsGatewaySender->to($dummyNumber);
        $this->assertSame($dummyNumber, $this->smsGatewaySender->receiver());
    }

    /** @test */
    function a_phone_can_have_plus_sign_in_the_beginning_but_is_stripped_when_setted_up()
    {
        $dummyNumber = '+359888888881';
        $this->smsGatewaySender->to($dummyNumber);
        $this->assertSame(['359888888881'], $this->smsGatewaySender->receiver());
    }

    /** @test */
    function a_local_number_as_a_receiver_throws_an_exception()
    {
        try {
            $dummyNumber = '088888888';
            $this->smsGatewaySender->to($dummyNumber);
        } catch(\Exception $exception) {
            $this->assertInstanceOf(BadRequestException::class, $exception);
            return;
        }
        $this->fail('An exception was not thrown but expected');
    }

    /** @test */
    function a_from_number_can_be_set()
    {
        $dummyNumber = '359888888888';
        $this->smsGatewaySender->from($dummyNumber);
        $this->assertSame($dummyNumber, $this->smsGatewaySender->sender());
    }

    /** @test */
    function a_message_can_be_set()
    {
        $dummyMessage = '359888888888';
        $this->smsGatewaySender->text($dummyMessage);
        $this->assertSame($dummyMessage, $this->smsGatewaySender->message());
    }

    /** @test */
    function a_message_cannot_be_sent_without_data()
    {
        try {
            $this->smsGatewaySender->send();
        } catch(\Exception $exception) {
            $this->assertInstanceOf(BadRequestException::class, $exception);
            return;
        }
        $this->fail('An exception was not thrown but expected');
    }

}