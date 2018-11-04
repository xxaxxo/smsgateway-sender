<?php

namespace xXc\SmsGatewaySender;

use PHPUnit_Framework_TestCase;
use xXc\SmsGatewaySender\Request\Message\CyrillicMessage;

class CyrillicMessageTest extends PHPUnit_Framework_TestCase
{

    /** @test */
    function a_cyrillic_message_will_be_converted_to_latin()
    {
        $cyrillicMessage = new CyrillicMessage();
        $message = "тест съобщение";
        $cyrillicMessage->validate($message);
        $this->assertSame('test suobshtenie', $message);
    }

    /** @test */
    function a_non_cyrillic_message_will_not_be_changed()
    {
        $cyrillicMessage = new CyrillicMessage();
        $message = "test message";
        $cyrillicMessage->validate($message);
        $this->assertSame('test message', $message);
    }

    /** @test */
    function a_message_with_more_than_160_chars_will_not_be_validated()
    {
        $cyrillicMessage = new CyrillicMessage();
        $message = "a message with more than a hundred and sixty characters in it: dummy data,dummy data dummy data,dummy data dummy data,dummy data dummy data,dummy data dummy data,dummy data dummy data,dummy data";
        $this->isFalse($cyrillicMessage->validate($message));
    }

    /** @test */
    function a_cyrillic_message_is_not_validated_if_it_goes_beyond_160_chars_after_conversion()
    {
        $cyrillicMessage = new CyrillicMessage();
        $message = "съобщение по-малко от 160 символа преди конвертирането, но по-голямо от 160 символа след преконвертиране. ЩшЩШщшщшщшщшщшщшщшшщшщщшщшшщщшщшщшщшщш";
        $this->isFalse($cyrillicMessage->validate($message));
    }

    /** @test */
    function a_mixed_message_between_cyrillic_and_latin_gets_converted_properly()
    {
        $cyrillicMessage = new CyrillicMessage();
        $message = "съобщение със смесени кирилица, and latin characters";
        $cyrillicMessage->validate($message);
        $this->assertSame('suobshtenie sus smeseni kirilica, and latin characters', $message);
    }

}