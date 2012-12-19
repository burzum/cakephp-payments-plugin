<?php
App::uses('Object', 'Core');
App::uses('CakeResponse', 'Network');
App::uses('CakeRequest', 'Network');
App::uses('PaymentProcessorException', 'Payments.Error');
App::uses('PaymentApiException', 'Payments.Error');
App::uses('PaymentApiLog', 'Payments.Log');
App::uses('PaymentStatus', 'Payments.Payment');

/**
 * BasePaymentProcessor
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
abstract class BasePaymentProcessor extends Object {

/**
 * Configuration settings for this processor
 *
 * @var array
 */
	public $config = array();

/**
 * Callback Url
 *
 * @var string callback url
 */
	public $callbackUrl = '/';

/**
 * Return Url
 *
 * @var string callback url
 */
	public $returnUrl = '/';

/**
 * Cancel Url
 *
 * @var string callback url
 */
	public $cancelUrl = '/';

/**
 * Finishing page url to display a thank you page or something like that
 *
 * @var string callback url
 */
	public $finishUrl = '/';

/**
 * CakeRequest object instance
 * 
 * @var CakeRequest
 */
	protected $_request;

/**
 * CakeResponse object instance
 * 
 * @var CakeResponse
 */
	protected $_response;

/**
 * Constructor
 *
 * @param array $options
 * @return \BasePaymentProcessor
 * @throws PaymentProcessorException
 */
	public function __construct(array $options = array()) {
		$this->config($options);

		if (!$this->_initialize($options)) {
			throw new PaymentProcessorException(__('Failed to initialize %s!', get_class($this)));
		}
	}

/**
 * Callback to avoid overloading the constructor if you need app or processor specific changes
 *
 * @param array $options
 * @return void
 */
	protected function _initialize(array $options) {
		if (!empty($options['request'])) {
			$this->_request = $options['request'];
		} else {
			$this->_request = new CakeRequest();
		}

		if (!empty($options['response'])) {
			$this->_response = $options['response'];
		} else {
			$this->_response = new CakeResponse();
		}

		return true;
	}

/**
 * Sets configuration data
 *
 * @param array $config
 * @param boolean $merge
 * @return void
 */
	public function config(array $config = array(), $merge = false) {
		if (empty($config)) {
			$config = (array) Configure::read(get_class($this));
		}

		if ($merge === true) {
			$this->config = array_merge($this->config, $config);
		} else {
			$this->config = $config;
		}
	}

/**
 * Redirect
 *
 * @param string|array url to redirect to
 */
	public function redirect($url) {
		header('Location: ' . $url);
		exit();
	}

/**
 * Log
 *
 * @param string $message
 * @param string $type
 * @return bool|void
 */
	public function log($message, $type = null) {
		if (empty($type)) {
			$type = 'payments_' . Inflector::underscore(__CLASS__);
		}

		parent::log($message, $type);
	}

/**
 * Check of the processor supports subscriptions
 *
 * @return boolean
 */
	public function supportsRecurringPayments() {
		return in_array('RecurringPaymentsInterface', class_implements($this));
	}

/**
 *
 */
	public function pay() {
		return;
	}

/**
 *
 */
	public function callback() {
		return;
	}

/**
 *
 */
	public function refund() {
		return;
	}

/**
 *
 */
	public function cancel() {
		return;
	}

}