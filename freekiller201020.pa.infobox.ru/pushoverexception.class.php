<?php

// /**
//  * Pushover exception class, needs to operate with an array of mesages
//  *
//  * @author Yuriy Gorbachev <urvindt@gmail.com>
//  * @version 0.1
//  * @package pushover
//  * @example index.php
//  * @link https://pushover.net/api
//  *
//  */
class PushoverException extends Exception
{
    // /**
    //  * Messages array
    //  * @var array
    //  */
    private $fMessages;

    // /**
    //  * Exception constructor
    //  * @param array $aMessages An array of messages
    //  */
    public function __construct(array $aMessages)
    {
        parent::__construct('PushoverException exception');
        $this->fMessages = $aMessages;
    }

    // /**
    //  * Get messages array
    //  * @return array
    //  */
    public function getMessages()
    {
        return empty($this->fMessages) ? array() : $this->fMessages;
    }
}
