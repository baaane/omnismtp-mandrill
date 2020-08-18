# OmniSmtp-Mandrill

###### An SMTP driver for OmniSmtp Processing library for PHP

# Usage

```php
<?php

$sendinblue = OmniSmtp::create(\Baaane\OmniSmtp\Mandrill::class, 'test-api-key');

$sendinblue->setSubject('The Mail Subject')
           ->setFrom('john.doe@example.com')
           ->setRecipients([
                [
                    'email' => 'jane.doe@gmail.com',
                    'name' => 'Jane Doe'
                ]
           ])
           ->setContent('<p>Hello From Mandrill OmniSmtp</p>')
           ->send();
```
