<?php

namespace Baaane\OmniSmtp;

use Ixudra\Curl\CurlService;
use OmniSmtp\Common\ProviderInterface;
use OmniSmtp\Exceptions\OmniMailException;

class Mandrill implements ProviderInterface
{
    const FROM_EMAIL = 'from_email' ;
    const HTML = 'html';
    const TO = 'to';

    protected $data_key = null;

    protected $container = [];

    public function __construct(string $apikey)
    {
        $this->setApiKey($apikey);
    }

    /**
     * @inheritDoc
     */
    public function getSmtpEndpoint()
    {
        return 'https://mandrillapp.com/api/1.0/messages/send.json';
    }

    /**
     * Set authorization header name
     *
     * @param string $bearer
     * 
     * @return $this
     */
    public function setAuthorizationHearerName(string $bearer = 'Authorization')
    {
        return $this->setData(self::AUTHORIZATION_NAME, $bearer);
    }

    /**
     * Get authorization  header name
     *
     * @return string
     */
    public function getAuthorizationHeaderName()
    {
        return $this->getData(self::AUTHORIZATION_NAME) ? $this->getData(self::AUTHORIZATION_NAME) : 'api-key';
    }

    /**
     * Send email
     *
     * @return true
     */
    public function send(CurlService $curl = null)
    {
        $curl = $curl ? $curl : new CurlService();

        $data = $this->formatDataBaseOnProvider();
        $response = $curl->to($this->getSmtpEndpoint())
                         ->returnResponseObject()
                         ->withData($data)
                         ->post();

        if (!in_array($response->status, [200, 201])) {
            throw OmniMailException::actualSendingEmailException(json_encode($response->content));
        }

        return true;
    }

    /**
     * Format data
     *
     * @return array
     */
    protected function formatDataBaseOnProvider()
    {
        $apikey = $this->getApikey();
        $from = $this->getFrom();
        $recipients = $this->getRecipients();
        $content = $this->getContent();
        $subject = ['subject' => $this->getSubject()];
        $other = [
            'async' => false,
            'ip_pool' => null,
            'send_at' => null,
            'track_opens' => true,
            'track_clicks' => true,
            'auto_text' => true
        ];
        
        $message = array_merge($content, $subject, $from, $recipients);
        $data = array_merge($apikey, ['message' => $message], $other);
        if ($this->data_key) {
            $data = [
                $this->data_key => array_merge($apikey, $from, $recipients, $subject, $content)
            ];
        }

        return $data;
    }

    /**
     * Set mail subject
     *
     * @param string $subject
     * 
     * @return $this
     */
    public function setSubject(string $subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * Get mail subject
     *
     * @return void
     */
    public function getSubject()
    {
         return $this->getData(self::SUBJECT);
    }

    /**
     * Set mail content. This is an html content
     *
     * @param string $html
     *
     * @return $this
     */
    public function setContent(string $html)
    {
        return $this->setData(self::HTML, [self::HTML => $html]);
    }

    /**
     * Get email html content
     *
     * @return $this
     */
    public function getContent()
    {
        return $this->getData(self::HTML);
    }

    /**
     * Set smtp sender
     *
     * Needs to be override by smtp providers
     *
     * @param string $from
     *
     * @return $this
     */
    public function setFrom(string $from)
    {
        return $this->setData(self::FROM_EMAIL, [self::FROM_EMAIL => $from]);
    }

    /**
     * Get sender
     *
     * @return mixed
     */
    public function getFrom()
    {
        return $this->getData(self::FROM_EMAIL);
    }

    /**
     * Set smtp recipients
     *
     * @param array $recipients
     *
     * @return $this
     */
    public function setRecipients(...$recipients)
    {
        $emails = [];
        foreach($recipients as $recipient){
            $emails['to'] = $recipient;
            unset($recipient);
        }

        return $this->setData(self::TO, $emails);
    }

    /**
     * Get recipients
     *
     * @return array
     */
    public function getRecipients()
    {
        return $this->getData(self::TO);
    }

    /**
     * Set SMTP apikey
     *
     * @param string $apikey
     * 
     * @return $this
     */
    public function setApiKey(string $apikey)
    {
        return $this->setData(self::APIKEY, ['key' => $apikey]);
    }

    /**
     * Get SMTP api key
     *
     * @return string|null
     */
    public function getApikey()
    {
        return $this->getData(self::APIKEY);
    }

    /**
     * Data setter
     *
     * @param string $key
     * @param mixed $value
     * 
     * @return $this
     */
    public function setData(string $key, $value)
    {
        $this->container[$key] = $value;
        return $this;
    }

    /**
     * Getter
     *
     * @param string $key
     * 
     * @return mixed
     */
    public function getData(string $key)
    {
        if (isset($this->container[$key])) {
            return $this->container[$key];
        }

        return null;
    }
}