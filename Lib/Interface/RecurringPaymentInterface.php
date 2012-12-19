<?php
/**
 * RecurringPaymentsInterface
 *
 * @author Florian Kramer
 * @copyright 2012 Florian Kramer
 * @license MIT
 */
interface RecurringPaymentsInterface {

/**
 *
 */
	public function cancelSubscription($id, array $options = array());

/**
 *
 */
	public function createSubscription($data);

/**
 *
 */
	public function updateSubscription($data);

}