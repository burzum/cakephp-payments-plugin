<?php
/**
 * RecurringPaymentsInterface
 *
 * @author Florian Kramer
 * @copyright 2012 Florian Kramer
 * @license MIT
 */
interface CreditCardPaymentInterface {

/**
 *
 */
	public function authorize($data);

/**
 *
 */
	public function capture($data);

}