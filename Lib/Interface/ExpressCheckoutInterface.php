<?php
/**
 * Copyright 2009 - 2012, Cake Development Corporation
 *                        1785 E. Sahara Avenue, Suite 490-423
 *                        Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2009 - 2012, Cake Development Corporation
 * @link      http://cakedc.com
 * @package   Cart.Lib/Payment
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Interface a payment processor must implement to be used as provider for Cart express checkouts
 * 
 * Note: the callback "public function afterOrder($orderId);" can also be implemented.
 * 	It will be called after the Order was successfully created
 */
interface ExpressCheckoutInterface {
/**
 * Step 1 of the Express Checkout
 * Initializes the command on Payment provider side, and redirects to its website. If an error occurred an exception must be thrown
 * 
 * When coming back from the website the user must be redirected to:
 *  - on success: the current url with "express-checkout-return" named param set to any value (e.g payment provider name)
 *  - on error: the url passed in $options['cancelUrl']
 * 
 * @throws InvalidArgumentException When there are missing parameters to do the checkout
 * @throws RuntimeException When an unexpected error occurs
 * @param string $data Cart to checkout with exhaustive information (cf Cart::getExhaustiveCartInfo() return value)
 * @param array $options Options for the checkout. Passed values could be:
 * 	- cancelUrl: url the user must be redirected to in case of cancellation
 *  - authorizationOnly: boolean true if you only want to authorize the amount but not withdraw founds from client
 * @return void
 * @access public
 */
	public function ecInitAndRedirect($data, $options = array());

/**
 * Step 2 of the Express Checkout
 * Retrieve order information and user details from Payment provider website. If an error occurred an exception must be thrown
 * 
 * If the user cancelled or if a permission is missing, the user must be redirected to the url passed in $options['cancelUrl']
 * Otherwise information must be returned in an array.
 * 
 * @throws InvalidArgumentException When there are missing parameters to do the checkout
 * @throws RuntimeException When an unexpected error occurs
 * @param string $data Cart to checkout with exhaustive information (cf Cart::getExhaustiveCartInfo() return value)
 * @param array $options Options for the checkout. Passed values could be:
 * 	- cancelUrl: url the user must be redirected to in case of cancellation
 * @return array Order Information, with some or all of the following keys:
 * 	- Payment: Payment related data. They will be transmitted "as is" to ecProcessPayment method.
 * 	- ShippingAddress: Shipping address if mentioned on Payment provider website 
 * @access public
 */
	public function ecRetrieveInfo($data, $options = array());

/**
 * Step 3 of the Express Checkout
 * Process to the payment. If an error occurred an exception must be thrown
 * 
 * @throws InvalidArgumentException When there are incorrect parameters to do the payment
 * @throws RuntimeException When an unexpected error occurs
 * @param string $data Cart to checkout with exhaustive information (cf Cart::getExhaustiveCartInfo() return value)
 * @param array $options Options for the checkout. Passed values could be:
 * 	- Payment: Payment related data returned by the ecRetrieveInfo method.
 * @return mixed False on error, an array of payment information on success. This array must have the following keys:
 * 	- payment_status: status of the payment made (cf CartOrder->paymentTypes for a list of possible values)
 * 	- payment_reference: internal reference for the transaction
 *	- taxes: either the total tax amount for the order, or detailed array of taxes per item array(item_id => tax_amount), optional
 * @access public
 */
	public function ecProcessPayment($data, $options = array());

}