<?php 

namespace KHPaymentGW;

class CommunicationException extends \Exception
{
	public $code;
	public $message;

	function __construct($code, $message)
	{
		$this->code = $code;
		$this->message = $message;
	}

}