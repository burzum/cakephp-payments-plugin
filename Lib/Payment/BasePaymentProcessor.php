<?php
App::uses('Object', 'Core');
App::uses('PaymentProcessorException', 'Payments.Error');
App::uses('PaymentApiException', 'Payments.Error');
App::uses('PaymentApiLog', 'Payments.Log');
App::uses('PaymentStatus', 'Payments.Payment');
App::uses('Hash', 'Utility');

/**
 * BasePaymentProcessor
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
abstract class BasePaymentProcessor {

/**
 * Used to add OR conditions to field validations
 */
	const OR_CONDITION = 'OR';

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
 * Values to be used by the API implementation
 *
 * Structure of the array is:
 * MethodName/VariableName/OptionsArray
 *
 * @var array
 */
	protected $_fields = array();

/**
 * Validation rules
 *
 * @var array
 */
	protected $_fieldRules = array(
		'pay' => array(
			'amount' => array(
				'required' => true,
				'type' => array('integer', 'float')
			),
		),
		'refund' => array(
			'amount' => array(
				'required' => true,
				'type' => array('integer', 'float')
			),
		),
	);

/**
 * Transaction Id for processors that return one
 *
 * @var mixed
 */
	protected $_transactionId = null;

/**
 * Subscription Id for processors that implement subscriptions
 *
 * @var mixed
 */
	protected $_subscriptionId = null;

/**
 * Raw response of a payment processor
 *
 * @var mixed
 */
	protected $_rawResponse = null;

/**
 * Sandbox mode
 *
 * Used for check if a processor is in sandbox / testing mode or not, this
 * is important for a lot of processor to toggle between live and sandbox
 * API callbacks and URLs
 *
 * @var mixed boolean
 */
	protected $_sandboxMode = false;

/**
 * List of required configuration fields
 *
 * @var array
 */
	protected $_configFields = array();

/**
 * Log object instance
 *
 * @var object
 */
	protected $_log = null;

/**
 * Internal Payment API Version
 *
 * Can be used for checks to keep a processor compatible to different versions
 *
 * @var string
 */
	private $__apiVersion = '1.0';

/**
 * Constructor
 *
 * @param PaymentProcessorConfig $config
 * @param array $options
 * @return BasePaymentProcessor
 * @throws PaymentProcessorException
 */
	public function __construct($config, array $options = array()) {
		if (!$this->configure($config)) {
			throw new PaymentProcessorException(__('Failed to configure %s!', get_class($this)));
		}

		if (!$this->_initialize($options)) {
			throw new PaymentProcessorException(__('Failed to initialize %s!', get_class($this)));
		}

		$this->_initializeLogging($options);
	}

/**
 * Sets and gets the sandbox mode
 *
 * @param mixed boolean|null $sandboxMode
 * @return boolean
 * @throws RuntimeException
 */
	public function sandboxMode($sandboxMode = null) {
		if (is_null($sandboxMode)) {
			return $this->_sandboxMode;
		}

		if ($sandboxMode === true) {
			$this->_sandboxMode = true;
		} else {
			$this->_sandboxMode = false;
		}
	}

/**
 * Validates that all required configuration fields are present
 *
 * @param array $configData
 * @throws InvalidArgumentException
 * @return void
 */
	protected function _validateConfig($configData) {
		$passedFields = array_keys($configData);

		foreach ($this->_configFields as $requiredField) {
			if (!in_array($requiredField, $passedFields)) {
				throw new InvalidArgumentException(sprintf('Missing configuration value for %s!', $requiredField));
			}
		}
	}

/**
 * getTransactionId
 *
 * @return string
 */
	public function getTransactionId() {
		return $this->_transactionId;
	}

/**
 * getSubscriptionId
 *
 * @return string
 */
	public function getSubscriptionId() {
		return $this->_subscriptionId;
	}

/**
 * Get the raw API response
 *
 * @return mixed
 */
	public function getRawResponse() {
		return $this->_rawResponse;
	}

/**
 * Returns the Payments API version
 *
 * Use the return value of this method to compare versions to support more than
 * one version of the payments library if you want within the same processor
 */
	final protected function _version() {
		return $this->__apiVersion;
	}

/**
 * Empties the fields
 *
 * @return void
 */
	public function flushFields() {
		$this->_fields = array();
	}

/**
 * Sets data for API calls
 *
 * @param string $field
 * @param mixed $value
 * @return void
 */
	public function set($field, $value = null) {
		if (is_array($field)) {
			$this->_fields = array_merge($this->_fields, $field);
			return;
		}

		$this->_fields[$field] = $value;
	}

/**
 * Unsets a field
 *
 * @param string $field
 * @return void
 */
	public function unsetField($field) {
		unset($this->_fields[$field]);
	}

/**
 * Gets a field value from the set fields
 *
 * @param string $field
 * @param array $options
 * @return mixed
 * @throws PaymentProcessorException
 */
	public function field($field, $options = array()) {
		$defaultOptions = array(
			'required' => false,
			'default' => null);

		$options = array_merge($defaultOptions, $options);

		if (!isset($this->_fields[$field])) {
			if ($options['required'] === true) {
				throw new PaymentProcessorException(__('Required value %s is not set!', $field));
			}

			if ($options['default'] !== null) {
				return $options['default'];
			}
		}

		return Hash::get($this->_fields, $field);
	}

/**
 * Validates if all (required) values are set for an API call
 *
 * You really should validate if all values are set before you do anything in
 * one of your methods to avoid the need to do a lot of manual checks on the
 * set data and to ensure that your API call is going to get all required values
 *
 * @param string $action
 * @throws PaymentProcessorException
 * @return boolean
 */
	public function validateFields($action) {
		if (isset($this->_fieldRules[$action])) {
			foreach($this->_fieldRules[$action] as $field => $options) {
				if ($field === self::OR_CONDITION) {
					$orResult = false;
					foreach($options as $innerField => $innerOptions) {
						try {
							if ($this->_validateField($innerField, $innerOptions)) {
								$orResult = true;
							}
						} catch (PaymentProcessorException $ex) {
							//ignore this field validation, because we are in an OR branch
						}
					}
					if (!$orResult) {
						$orFields = array_keys($options);
						throw new PaymentProcessorException(__('Required value is not set for at least one of those fields: %s', implode(', ', $orFields)));
					}
				}
				$this->_validateField($field, $options);
			}
		}

		return true;
	}

/**
 * Validate a specific field
 * @param type $field
 * @param type $options
 * @return type
 * @throws PaymentProcessorException
 */
	protected function _validateField($field, $options = array()) {
		$required = Hash::get($options, 'required');
		if ($required === true) {
			if (!isset($this->_fields[$field])) {
				throw new PaymentProcessorException(__('Required value %s is not set!', $field));
			}
		}

		if (isset($options['type'])) {
			if (is_string($options['type'])) {
				$options['type'] = array($options['type']);
			}

			$typeFound = false;
			foreach ($options['type'] as $type) {
				if ($this->validateType($type, $this->_fields[$field])) {
					$typeFound = true;
					break;
				} else {
					if (method_exists($this, $type)) {
						$method = '_' . $type;
						return $this->{$method}($action, $this->_fields[$field]);
					}
				}
			}

			if ($typeFound === false) {
				throw new PaymentProcessorException(__('Invalid data type for value %s!', $field));
			}
		}
		return true;
	}

/**
 * Validates values against data types
 *
 * @param string $type
 * @param mixed $value
 * @return bool
 */
	public function validateType($type, $value) {
		switch ($type) :
			case 'string':
				return is_string($value);
			case 'integer':
			case 'int':
				return is_int($value);
			case 'float':
				return is_float($value);
			case 'array':
				return is_array($value);
			case 'object':
				return is_object($value);
		endswitch;

		return false;
	}

/**
 * Callback to avoid overloading the constructor if you need to inject app or processor specific changes
 *
 * @param array $options
 * @throws RuntimeException
 * @return void
 */
	protected function _initialize(array $options) {
		return true;
	}

/**
 * Initializes the log object
 *
 * @param array $options
 * @throws RuntimeException
 * @return void
 */
	protected function _initializeLogging(array $options) {
		if (isset($options['logObject'])) {
			if (!isset($options['logObjectMethod'])) {
				if (!method_exists($options['logObject'], 'write')) {
					throw new RuntimeException(__('The log object must implement a method write($message, $logType)!'));
				}

				//$class = get_class($options['logObject']);
				//$p = new ReflectionParameter(array($class, 'write'), 0);
				//$p = new ReflectionParameter(array($class, 'write'), 0);
			}
		} else {
			$this->_log = new CakeLog();
		}
	}

/**
 * Sets configuration data, override it as needed
 *
 * PaymentProcessorConfig array $config
 * @internal param bool $merge
 * @param array $config
 * @return void
 */
	public function configure(array $config = array()) {
		$this->_validateConfig($config);
		$this->config = $config;
		return true;
	}

/**
 * Redirect
 *
 * @param string $url Url to redirect to
 */
	public function redirect($url) {
		header('Location: ' . (string) $url);
		exit();
	}

/**
 * Write to the log
 *
 * @param string $message
 * @param string $type
 * @return bool|void
 */
	public function log($message, $type = null) {
		if (is_null($this->_log)) {
			return false;
		}

		if (!is_string($message)) {
			$message = var_dump($message);
		}
		$type = 'payments-' . Inflector::underscore(__CLASS__) . '-' . $type;
		return $this->_log->write($message, $type);
	}

/**
 * Check of the processor supports a certain interface
 *
 * @param string $interfaceName
 * @return boolean
 */
	public function supports($interfaceName) {
		return in_array($interfaceName . 'Interface', class_implements($this));
	}

/**
 * Method to initialize (for processor like paypal) or send the payment directly
 *
 * @param float $amount
 * @param array $options
 * @return
 */
	abstract public function pay($amount, array $options = array());

/**
 * This method is used to process API callbacks
 *
 * API callbacks are usually notifications via HTTP POST or less common GET.
 *
 * This method should return a payment status
 */
	abstract public function notificationCallback(array $options = array());

/**
 * Refunds money
 *
 * @param $paymentReference
 * @param $amount
 * @param string $comment
 * @param array $options
 * @return
 * @internal param $float
 */
	abstract public function refund($paymentReference, $amount, $comment = '', array $options = array());

/**
 * Cancels a payment
 *
 * @param string $paymentReference
 * @param array $options
 * @return mixed
 */
	abstract public function cancel($paymentReference, array $options = array());

}