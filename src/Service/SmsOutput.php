<?php

namespace MessageBird\Service;

use MessageBird\Client;
use MessageBird\Objects\Message;

/**
 * Class SmsOutput
 * @package MessageBird\Service
 */
class SmsOutput
{
    /**
     * Private key for the account
     */
    const KEY = 'PRIVATE_KEY_INSERT_HERE';

    /**
     * Max length for concatenated sms
     */
    const SPLIT = 153;

    /**
     * Max length for one sms
     */
    const MAXLENGTH = 160;

    /**
     * Base UDH for on package of concatenated sms
     */
    const BASEUDH = "05000334";

    /**
     * @var $message Message received from post request
     */
    private $message;

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * The originator from the message received from post
     * @return mixed
     */
    public function getOriginator()
    {
        return $this->getMessage()['originator'];
    }

    /**
     * The receipient from the message received from post
     * @return mixed
     */
    public function getRecipients()
    {
        return $this->getMessage()['recipient'];
    }

    /**
     * Returns an array of messages to be sent by sms (one or multiple)
     * @return array
     */
    public function getBody()
    {
        if (strlen($this->getMessage()['message']) > self::MAXLENGTH) {
            $messages = str_split($this->getMessage()['message'], self::SPLIT);
        } else {
            $messages = array($this->getMessage()['message']);
        }

        return $messages;
    }

    /**
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * SmsOutput constructor.
     * @param $message
     */
    public function __construct($message)
    {
        $this->setMessage(json_decode($message,true));
    }

    /**
     * Create the arrays of sms messages to be sent
     * @return array
     */
    public function createSms()
    {
        $messages = array();
        $totalSplits = count($this->getBody());
        foreach ($this->getBody() as $key => $body) {
            $Message = new Message();
            $Message->originator = $this->getOriginator();
            $Message->recipients = $this->getRecipients();
            $Message->body = $body;
            if ($totalSplits > 1) {
                $total = sprintf("%02d", $totalSplits);
                $current = sprintf("%02d", $key+1);
                $udh = self::BASEUDH.$total.$current;
                $Message->type = 'binary';
                $Message->typeDetails = array("udh" => $udh);
            }
            $messages[] = $Message;
        }

        return $messages;
    }

    /**
     * Sends sms based on a message given
     * @param $message
     * @return message result
     */
    public function sendSms($messages)
    {
        $MessageBird = new Client(self::KEY);
        foreach ($messages as $message) {
            $result = $MessageBird->messages->create($message);
            sleep(1);
        }
        return $result;
    }
}
