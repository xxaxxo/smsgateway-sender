###Package for the AMDTelecom SMSGateway
This package is for standard composer use, but has optimisations for laravel usage.

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
