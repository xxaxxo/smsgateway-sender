<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 30.10.18
 */

namespace xXc\SmsGatewaySender;

use xXc\SmsGatewaySender\Exception\BadRequestException;
use xXc\SmsGatewaySender\Request\PhoneNumber\BulgarianPhoneNumber;

class SmsGatewaySender
{
    protected $phoneValidator = null;


    public function __construct($phoneValidator = null)
    {
        if (!is_null($phoneValidator)) {
            $this->phoneValidator = $phoneValidator;
        } else {
            $this->phoneValidator = new BulgarianPhoneNumber();
        }

    }

    protected $smsData = [
        'to' => [],
        'from' => null,
        'message' => null,
    ];

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

    public function from($phoneNumber)
    {
        $this->smsData['from'] = $phoneNumber;
        return $this;
    }

    public function message($message)
    {

    }

    public function send()
    {
        $this->validateData();
    }

    public function receiver()
    {
        return $this->smsData['to'];
    }

    public function sender()
    {
        return $this->smsData['from'];
    }

    private function validateData()
    {
        if (empty($this->smsData['to'])) {
            throw new BadRequestException(
                'No recipient has been set. You must use the ->to() method to set a receiver/s'
            );
        }
    }


}