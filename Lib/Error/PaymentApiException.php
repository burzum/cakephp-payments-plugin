<?php
/**
 * PaymentApiException
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
class PaymentApiException extends CakeException {

	public $apiErrorCode = null;
	public $apiErrorMessage = null;
	public $apiErrorReason = null;
}