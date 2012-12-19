<?php
App::uses('CakeLogInterface', 'Log');
/**
 * Payment Api Log
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 * @link http://book.cakephp.org/2.0/en/core-libraries/logging.html#creating-and-configuring-log-streams
 */
class PaymentApiLog implements CakeLogInterface {

/**
 * Constants
 */
	const LOG = 'payment';
	const WARNING = 'payment-warning';
	const ERROR = 'payment-error';
	const DEBUG = 'payment-debug';

/**
 * Options
 *
 * @var array
 */
	public $options = array();

/**
 * Log types to log to the payment api log
 *
 * @var array
 */
	public $types = array(
		'payment',
		'payment-debug',
		'payment-error',
		'payment-warning');

/**
 * Constructor
 *
 * @param array $options
 */
	public function __construct($options = array()) {
		if (isset($options['types'])) {
			$this->types = array_merge($this->types, $options['types']);
		}

		$this->options = $options;
	}

/**
 * Write to the log
 *
 * @param $type
 * @param $message
 * @return void
 */
	public function write($type, $message) {
		if (in_array($type, $this->types) || $type == 'payment-debug' && Configure::read('debug') > 0) {
			return;
		}

		if (!is_string($message)) {
			$message = print_r($message);
		}

		$this->_write($type, $message, $this->_getTrace());
	}

/**
 * Get the stack trace data
 *
 * @param integer levelsup in the trace to get the original place of the log
 * @return array
 */
	protected function _getTrace($levelUp = 3) {
		$trace = debug_backtrace();
		$data = array();

		if (isset($trace[$levelUp]) && isset($trace[$levelUp]['file']) && isset($trace[$levelUp]['line'])) {
			$data['file'] = $trace[$levelUp]['file'];
			$data['line'] = $trace[$levelUp]['line'];
			$data['trace'] = serialize($trace);
			$data['_post'] = serialize($_POST);
			$data['_get'] = serialize($_GET);
			$data['session'] = serialize($_SESSION);
		}

		return $data;
	}

/**
 *
 */
	protected function _write($type, $message, $data) {
		if (!isset($this->options['model'])) {
			$model = 'Payments.PaymentApiLog';
		} else {
			$model = $this->options['model'];
		}

		$model = ClassRegistry::init($model);
		$model->write($type, $message, $data);
	}

}