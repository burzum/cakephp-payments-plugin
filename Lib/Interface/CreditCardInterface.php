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
	public function authorize();

/**
 *
 */
	public function capture();

/**
 *
 */
	public function void($transactionId);

/**
 *
 */
	public function echeck($transactionId);

}