###Package for the AMDTelecom SMSGateway
This package is for standard php project use, but has optimisations for laravel usage.

##Installation
@todo - document the installation process

##Usage

Desired usage
```php
    try {
        $smsGatewaySender = new \xXc\SmsGatewaySender();
        $response = $smsGatewaySender
            ->from('')
            ->to('')
            ->message('')
            ->send();
        if ($response->isSent()) {
			//do whatever
        } else {
			//log errors $response->errors
			//return error message
        }
    } catch (\Exception $exception) {
		//log data
		//return error based on the exception message?!
    }
```

overriding with custom validators can be done via the constructor or via the setters
All the custom validator must extend the Validator interface 
```php
$smsGatewaySender = new \xXc\SmsGatewaySender($customPhoneValidator, $customMessageValidator);
\\or

$smsGatewaySender = new \xXc\SmsGatewaySender();
$smsGatewaySender->setPhoneValidator($customPhoneValidator);
$smsGatewaySender->setMessageValidator($customMessageValidator);
```

Contributing:
You can create custom validators for phone numbers or text messages (if needed) and create pull requests

