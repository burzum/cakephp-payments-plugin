<?php
/**
 * RecurringPaymentInterface
 *
 * @author Florian Kramer
 * @copyright 2012 Florian Kramer
 * @license MIT
 */
interface RecurringPaymentInterface {

/**
 * Cancels a subscription
 *
 * @param string
 * @param array
 */
	public function cancelSubscription($transactionReference, array $options = array());

/**
 * Creates a new subscription
 *
 * @param array $options
 * @return
 */
	public function createSubscription($options = array());

/**
 * Updates a subscription
 *
 * @param string
 * @param array
 */
	public function updateSubscription($transactionReference, $options = array());

}