<?php
App::uses('CakeSession', 'Model/Datasource');
App::uses('CartAppModel', 'Cart.Model');
/**
 * Payment Api Log Model
 *
 * This model is used to log all payment API actions it can be called
 * directly or used with a CakeLog engine that can load models. For the ease of
 * use there is a logger in the cart plugin: PaymentApiLogger.
 *
 * You want that data very likely for
 * - debugging
 * - legal reasons
 * - statistics
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 * @link http://book.cakephp.org/2.0/en/core-libraries/logging.html#creating-and-configuring-log-streams
 */
class PaymentApILog extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * Initializes a new api transaction session
 *
 * @param string $processorClass
 * @param string $orderId Order UUID
 * @return mixed
 */
	public function initialize($processorClass, $orderId) {
		$token = $this->token();

		CakeSession::write('Payment', array(
			'token' => $token,
			'processor' => $processorClass));

		CakeSession::write('Payment.orderId', $orderId);
		CakeSession::write('Payment.token', $token);
		CakeSession::write('Payment.processor', $processorClass);

		$this->write('payment', __d('cart', 'Payment process started'));

		return $token;
	}

/**
 * Generates the token
 *
 * @return string
 */
	public function token() {
		return str_replace('-', '', String::uuid());
	}

/**
 * Finishes a payment transaction log by deleting the session keys
 *
 * @return void
 */
	public function finish() {
		$this->write('payment', __d('cart', 'Payment process finished'));
		CakeSession::delete('Payment');
	}

/**
 * Writes to the log table
 *
 * @param string $type
 * @param mixed $message
 * @param $data
 * @return mixed Array on success else false
 */
	public function write($type, $message, $data = array()) {
		extract($this->readSessionVars());

		if (empty($token) || empty($processorName) || empty($orderId)) {
			return false;
		}

		$this->create();
		return $this->save(array(
			$this->alias => array(
				'processor' => $processorName,
				'token' => $token,
				'order_id' => $orderId,
				'type' => $type,
				'message' => $message)));
	}

/**
 *
 */
	public function readSessionVars() {
		return array(
			'processorName' => CakeSession::read('Payment.processor'),
			'token' => CakeSession::read('Payment.token'),
			'orderId' => CakeSession::read('Payment.orderId'));
	}
}
