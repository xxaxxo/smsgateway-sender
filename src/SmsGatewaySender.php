<?php
/**
 * Created by: Michael Kumar <m.kumar@abv.bg>
 * Github: https://github.com/xxaxxo/
 * Date: 30.10.18
 */

namespace xXc\SmsGatewaySender;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Config;
use xXc\SmsGatewaySender\Exception\BadRequestException;
use xXc\SmsGatewaySender\Request\Message\CyrillicMessage;
use xXc\SmsGatewaySender\Request\PhoneNumber\BulgarianPhoneNumber;
use xXc\SmsGatewaySender\Request\Validator;


class SmsGatewaySender
{
    public $errors = [];
    public $messageId = null;
    protected $phoneValidator = null;
    protected $messageValidator = null;
    protected $client = null;
    protected $url = null;
    protected $query = null;
    protected $smsData = [
        'to' => [],
        'from' => null,
        'text' => null,
    ];
    private $knownErrors = [
        '',
    ];

    /**
     * SmsGatewaySender constructor.
     * @param null $phoneValidator
     * @param null $messageValidator
     */
    public function __construct(
        Validator $phoneValidator = null,
        Validator $messageValidator = null,
        Client $client = null
    ) {
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


        if (!is_null($client)) {
            $this->client = $client;
        } else {
            $this->client = new Client();
        }
    }

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
        $this->url = $this->configure('config.host').':'.$this->configure('config.port');
        $this->query = array_merge(
            $this->smsData,
            [
                'username' => $this->configure('config.username'),
                'password' => $this->configure('config.password'),
            ]
        );
        $fullQueryParams = [
            'verify' => false,
            'query' => $this->query,
        ];

        return $this->parseResponse($this->client->get($this->url.http_build_query($this->query), $fullQueryParams)->getBody()->getContents());
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

    public function configure($param)
    {
        if (!function_exists('config')) {
            preg_match('{^config\.(?<keyword>[a-z]+)$}', $param, $match);
            $conf = new Repository(array_merge(require __DIR__.'/../config/smsgateway_sender.php', []));

            return $conf->get($match['keyword']);
        } else {
            return config($param);
        }
    }

    public function isSent()
    {
        return $this->messageId != null;
    }

    public function parseResponse($response)
    {
        if (preg_match(
            '{success\s(MessageId|Message\sId):\s(?<messageId>[a-z0-9\-]+)\;(\sRecipient:|Recipient:)(?<recepient>[0-9]+)}i',
            $response,
            $success
        )) {
            $this->messageId = $success['messageId'];
        } else {
            if (preg_match(
                '{error:(?<errorCode>[0-9]+);[\s]?(status\:)?(?<errorMessage>[\w\d\s\[\]\.\,\(\)\-]+)$}i',
                $response,
                $error
            )) {
                $this->errors[] = [
                    'code' => $error['errorCode'],
                    'message' => $error['errorMessage'],
                    'url' => $this->url,
                    'query' => $this->query,
                ];
            } else {
                $this->errors[] = [
                    'code' => null,
                    'message' => $response,
                    'url' => $this->url,
                    'query' => $this->query,
                ];
            }


        }

        return $this;
    }

    public function errors()
    {
        return $this->errors;
    }


}