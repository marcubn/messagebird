<?php

namespace MessageBird\Service;

/**
 * Class Validator
 * @package MessageBird\Service
 */
class Validator
{
    /**
     * Message to be validate
     * @var $message
     */
    private $message;

    /**
     * Check if message is correct
     * @var bool
     */
    private $hasErrors = false;

    /**
     * Stores errors from validation
     * @var array
     */
    private $errors = array();

    /**
     * @param $error
     */
    public function setErrors($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $error
     */
    public function setHasErrors($error)
    {
        $this->hasErrors = $error;
    }

    /**
     * @return bool
     */
    public function checkHasErrors()
    {
        return $this->hasErrors;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Validator constructor.
     * @param $message
     */
    public function __construct($message)
    {
        $this->setMessage($message);
    }

    /**
     * Validates the received message
     * Further advanced validation can be added
     */
    public function validateReceivedMessage()
    {
        $message = json_decode($this->getMessage(), true);

        // slight verification of received message
        if (empty($message['recipient']) || empty($message['originator']) || empty($message['message'])) {
            $this->setHasErrors(true);
            $this->setErrors('All fields are mandatory');
        }

        // see if phone number contains only number
        if (!is_numeric($message['recipient'])) {
            $this->setHasErrors(true);
            $this->setErrors('Phone must be a number');
        }
    }
}
