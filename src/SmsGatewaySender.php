<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 30.10.18
 */

namespace xXc\SmsGatewaySender;

use xXc\SmsGatewaySender\Exception\BadRequestException;
use xXc\SmsGatewaySender\Request\Message\CyrillicMessage;
use xXc\SmsGatewaySender\Request\PhoneNumber\BulgarianPhoneNumber;
use xXc\SmsGatewaySender\Request\Validator;

class SmsGatewaySender
{
    protected $phoneValidator = null;
    protected $messageValidator = null;

    /**
     * SmsGatewaySender constructor.
     * @param null $phoneValidator
     * @param null $messageValidator
     */
    public function __construct(Validator $phoneValidator = null, Validator $messageValidator = null)
    {
        if (!is_null($phoneValidator)) {
            $this->phoneValidator = $phoneValidator;
        } else {
            $this->phoneValidator = new BulgarianPhoneNumber();
        }

        if (!is_null($messageValidator)) {
            $this->messageValidator = $messageValidator;
        } else {
            $this->messageValidator = new CyrillicMessage();
        }
    }

    protected $smsData = [
        'to' => [],
        'from' => null,
        'text' => null,
    ];

    /**
     * setter for a receiver/s
     * accepts string (phoneNumber) or array of phoneNumbers for multiple receivers
     * @param $phoneNumber
     * @return $this
     * @throws BadRequestException
     */
    public function to($phoneNumber)
    {
        if (!is_array($phoneNumber)) {
            $phoneNumber = (array)$phoneNumber;
        }

        foreach ($phoneNumber as $k => $item) {
            if ($this->phoneValidator->validate($item) === false) {
                throw new BadRequestException('Receiver phone: '.$item.' is not valid according to the validator');
            }
            $phoneNumber[$k] = $item;
        }

        $this->smsData['to'] = array_merge($this->smsData['to'], $phoneNumber);

        return $this;
    }

    /**
     * setter for from number (sender)
     * @param $phoneNumber
     * @return $this
     */
    public function from($phoneNumber)
    {
        $this->smsData['from'] = $phoneNumber;

        return $this;
    }

    /**
     * setter for message text
     * @param $text
     * @return $this
     */
    public function text($text)
    {
        $this->messageValidator->validate($text);
        $this->smsData['text'] = $text;
        return $this;
    }

    /**
     * actual sending of a message
     */
    public function send()
    {
        $this->validateData();
    }

    /**
     * getter for a receiver
     * @return mixed
     */
    public function receiver()
    {
        return $this->smsData['to'];
    }

    /**
     * getter for the sender
     * @return mixed
     */
    public function sender()
    {
        return $this->smsData['from'];
    }

    /**
     * getter for the message body
     * @return mixed
     */
    public function message()
    {
        return $this->smsData['text'];
    }

    /**
     * setter for a custom phone validator
     * @param Validator $validator
     * @return $this
     */
    public function setPhoneValidator(Validator $validator)
    {
        $this->phoneValidator = $validator;
        return $this;
    }

    /**
     * setter for a custom message validator
     * @param Validator $validator
     * @return $this
     */
    public function setMessageValidator(Validator $validator)
    {
        $this->messageValidator = $validator;
        return $this;
    }

    /**
     * validates the data before sending
     * @throws BadRequestException
     */
    private function validateData()
    {
        if (empty($this->smsData['to'])) {
            throw new BadRequestException(
                'No recipient has been set. You must use the ->to() method to set a receiver/s'
            );
        }

        if (is_null($this->smsData['from'])) {
            throw new BadRequestException(
                'No sender has been set. You must use the ->from() method to set a sender'
            );
        }

        if (is_null($this->smsData['text'])) {
            throw new BadRequestException(
                'No text for a message has been set. You must use the ->text() method to set it'
            );
        }
    }


}