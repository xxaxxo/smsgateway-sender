<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 30.10.18
 */

namespace xXc\SmsGatewaySender;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
    function all_the_data_can_be_chained_on_request()
    {
        $dummyMessage = 'test message';
        $dummyNumber = '359888888888';
        $this->smsGatewaySender
            ->text($dummyMessage)
            ->from($dummyNumber)
            ->to($dummyNumber);
        $this->assertSame($dummyMessage, $this->smsGatewaySender->message());
        $this->assertSame($dummyNumber, $this->smsGatewaySender->sender());
        $this->assertSame([$dummyNumber], $this->smsGatewaySender->receiver());
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

    /** @test */
    function a_message_can_be_sent()
    {
            $successMessage = 'SUCCESS MessageId: 5093c47680aa7-5267-87898; Recipient:44201112345674';
            $fakeClient = \Mockery::mock('GuzzleHttp\Client');
            $fakeClient->shouldReceive('get->getBody->getContents')->andReturn($successMessage);

            $dummyMessage = 'test message';
            $dummyNumber = '359888888888';
            $smsGatewaySender = new SmsGatewaySender(null, null, $fakeClient);
            $response = $smsGatewaySender
                ->from($dummyNumber)
                ->to($dummyNumber)
                ->text($dummyMessage)
                ->send();
            $this->assertTrue($response->isSent());
            $this->assertEmpty($response->errors());
    }

    /** @test */
    function a_message_can_have_errors_on_senging()
    {
            $errorMessage = 'ERRNO 302: Failed to send message';
            $fakeClient = \Mockery::mock('GuzzleHttp\Client');
            $fakeClient->shouldReceive('get->getBody->getContents')->andReturn($errorMessage);

            $dummyMessage = 'test message';
            $dummyNumber = '359888888888';
            $smsGatewaySender = new SmsGatewaySender(null, null, $fakeClient);
            $response = $smsGatewaySender
                ->from($dummyNumber)
                ->to($dummyNumber)
                ->text($dummyMessage)
                ->send();
            $this->assertFalse($response->isSent());
            $this->assertNotEmpty($response->errors());
    }

}