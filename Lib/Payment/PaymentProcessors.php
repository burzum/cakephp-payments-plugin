<?php
App::uses('MissingPaymentProcessorException', 'Payments.Error');
/**
 * PaymentProcessors
 *
 * This class basically just acts as a loader for payment processors
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
class PaymentProcessors {

/**
 * Loaded processors
 *
 * @var array
 */
	protected $_loaded = array();

/**
 * Return a singleton instance of the PaymentProcessors.
 *
 * @return PaymentProcessors instance
 */
	public static function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] = new PaymentProcessors();
		}
		return $instance[0];
	}

/**
 * Loads a new payment processor
 *
 * @param string $processor
 * @param $config
 * @param array $options
 * @throws MissingPaymentProcessorException
 * @return Processor Instance
 */
	public static function load($processor, $config, $options = array()) {
		$_this = PaymentProcessors::getInstance();

		if (substr($processor, -9) != 'Processor') {
			$processor = $processor . 'Processor';
		}

		if (isset($_this->_loaded[$processor])) {
			return $_this->_loaded[$processor];
		}

		list($plugin, $class) = pluginSplit($processor, true);

		App::uses($class, $plugin . 'Lib/Payment');
		if (!class_exists($class)) {
			throw new MissingPaymentProcessorException(array(
				'class' => $class,
				'plugin' => substr($plugin, 0, -1)));
		}

		$_this->_loaded[$processor] = new $class($config, $options);
		return $_this->_loaded[$processor];
	}

}
