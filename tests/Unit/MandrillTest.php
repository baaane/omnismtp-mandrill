<?php

namespace Tests\Unit;

use OmniSmtp\OmniSmtp;
use Mandrill\Tests\TestCase;
use Ixudra\Curl\CurlService;

class MandrillTest extends TestCase
{
    public function testMandrillEmail()
    {
        $mandrill = OmniSmtp::create(\Baaane\OmniSmtp\Mandrill::class, 'test-api-key');

        $response = $mandrill->setSubject('The Mail Subject')
                   ->setFrom('john.doe@gmail.com')
                   ->setRecipients([
                        [
                            'email' => 'jane.doe@gmail.com',
                            'name' => 'Jane Doe'
                        ]
                   ])
                   ->setContent('<p>Hello From Mandrill OmniMail</p>')
                   ->send();

        $this->assertTrue($response);
    }
}