<?php
namespace MessageBird\Command;

use MessageBird\Service\SmsOutput;
use MessageBird\Service\Validator;

/**
 * Class RunCommand
 * @package MessageBird\Command
 */
class RunCommand
{
    /**
     * @var $post
     */
    public $post;

    /**
     * RunCommand constructor.
     * @param $post
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return string
     */
    public function execute()
    {
        $file = "lock.txt";
        $f = fopen($file, 'r');
        $line = fgets($f);
        fclose($f);
        if($line == 0)
        {
            file_put_contents($file, "1");
            // check message received from post
            $validator = new Validator($this->getPost());
            $validator->validateReceivedMessage();

            if ($validator->checkHasErrors()) {
                return implode("\n", $validator->getErrors());
            } else {
                // create message to be sent
                $smsOutput = new SmsOutput($this->getPost());
                $messages = $smsOutput->createSms();
                sleep(5);
                $smsOutput->sendSms($messages);

            }
            file_put_contents($file, "0");
            return 'sms sent';
        }
        else
        {
            sleep(1);
            $this->execute();
        }

    }

}
