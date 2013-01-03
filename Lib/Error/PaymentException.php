<?php
/**
 * PaymentException
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class PaymentException extends Exception {

	protected $_attributes = null;

	protected $_messageTemplate = null;

	public function __construct($message, $code = null) {
		if (is_array($message)) {
			$this->_attributes = $message;
			$message = sprintf($this->_messageTemplate, $message);
		}

		parent::__construct($message, $code);
	}

}