# SendOtp Library

This PHP library allows you to send OTP messages via the MSG91 API. It is built for PHP 8.2 and adheres to modern PHP best practices.

## Installation

Install via Composer:

```bash
composer require er.dhruvmishra/sendotp

```
# USAGE
```php

<?php
require 'vendor/autoload.php';

use SendOtp\Client;

$sendOtp = new Client('YOUR_AUTH_KEY');

// Optionally, set sender and custom message
$sendOtp->setSender('SENDERID')->setMessage('Your OTP is: {otp}');

$response = $sendOtp->send('9876543210', 'YOUR_TEMPLATE_ID');

print_r($response);
?>
```

## License
This library is open-sourced software licensed under the MIT license.


