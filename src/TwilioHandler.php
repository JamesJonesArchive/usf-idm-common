<?php

namespace USF\IdM;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;


/**
 * Sends notifications through SMS using the twilio api
 *
 * @author Eric PIerce <epierce@usf.edu>
 * @see    http://www.twilio.com/docs/api/rest
 */
class TwilioHandler extends AbstractProcessingHandler
{
    private $twilio_client;
    private $to_numbers = [];
    private $from_numbers = [];


    /**
     * @param string       $account_sid       Twilio api account sid
     * @param string       $auth_token        Twilio api access token
     * @param string|array $from_numbers      Phone number or array of numbers (one is selected randomly)
     *                                          the message will be sent from.
     * @param string|array $to_numbers        Phone number or array of numbers the message will be sent to
     * @param integer      $level             The minimum logging level at which this handler will be triggered
     * @param Boolean      $bubble            Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($account_sid, $auth_token, $from_numbers, $to_numbers, $level = Logger::CRITICAL, $bubble = true)
    {
        $this->twilio_client = new \Services_Twilio($account_sid, $auth_token);
        $this->to_numbers = (array) $to_numbers;
        $this->from_numbers = (array) $from_numbers;

        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        foreach ($this->to_numbers as $to_number) {
            $this->twilio_client->account->messages->sendMessage(
                $this->getFromNumber(),
                $to_number,
                $record['formatted']
            );
        }

    }

    private function getFromNumber(){
        return $this->from_numbers[array_rand($this->from_numbers, 1)];
    }
}