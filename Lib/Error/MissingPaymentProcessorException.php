<?php
/**
 * MissingPaymentProcessorException
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
class MissingPaymentProcessorException extends CakeException {

/**
 * Message template
 *
 * @var string
 */
	protected $_messageTemplate = 'Payment Processor class %s could not be found.';

}