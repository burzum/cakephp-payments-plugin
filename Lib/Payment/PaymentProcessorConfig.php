<?php
/**
 * The payment processor config class handles different configuration sets and
 * sandbox configurations for payment processors and error handling in the
 * case of missing configuration sets
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
class PaymentProcessorConfig implements ArrayAccess {
/**
 *
 */
	protected $_sandboxMode = false;

/**
 *
 */
	protected $_sandboxConfig = array();

/**
 *
 */
	protected $_liveConfig = array();

/**
 *
 */
	protected $_activeConfig = 'default';

/**
 *
 */
	protected $_requiredFields = array();

/**
 *
 */
	public function __construct($configData, $name = 'default' , $sandbox = false) {
		$this->setConfig($configData, $name, $sandbox);
	}

/**
 *
 */
	public function uses($configName = 'default', $sandbox = false) {
		if ($this->_sandboxMode === true || $sandbox === true) {
			if (!isset($this->_sandboxConfig[$configName])) {
				throw new InvalidArgumentException(sprintf('The config %s does not exist in the sandbox configuration set!', $configName));
			}
			$this->_activeConfig = $configName;
		} else {
			if (!isset($this->_liveConfig[$configName])) {
				throw new InvalidArgumentException(sprintf('The config %s does not exist in the live configuration set!', $configName));
			}
			$this->_activeConfig = $configName;
		}
	}

/**
 * Validates that all configuration is present
 *
 * @param $configData
 * @throws InvalidArgumentException
 * @return void
 */
	protected function _validateFields($configData) {
		$passedFields = array_keys($configData);

		foreach ($this->_requiredFields as $requiredField) {
			if (!in_array($requiredField, $passedFields)) {
				throw new InvalidArgumentException(sprintf('Missing configuration value for %s!', $requiredField));
			}
		}
	}

/**
 *
 */
	public function setConfig($configData, $configName, $sandbox = false) {
		$this->_validateFields($configData);

		if ($sandbox) {
			$this->_sandboxConfig[$configName] = $configData;
		} else {
			$this->_liveConfig[$configName] = $configData;
		}
	}

/**
 *
 */
	protected function _getConfigString() {
		$config = '_liveConfig';
		if ($this->_sandboxMode === true) {
			$config = '_sandboxConfig';
		}

		return $config;
	}

/**
 *
 */
	public function offsetSet($offset, $value) {
		$config = $this->_getConfigString();

		if (is_null($offset)) {
			$this->{$config}[$this->_activeConfig][] = $value;
		} else {
			$this->{$config}[$this->_activeConfig][$offset] = $value;
		}
	}

/**
 *
 */
	public function offsetExists($offset) {
		$config = $this->_getConfigString();
		return isset($this->{$config}[$this->_activeConfig][$offset]);
	}

/**
 *
 */
	public function offsetUnset($offset) {
		$config = $this->_getConfigString();
		unset($this->{$config}[$this->_activeConfig][$offset]);
	}

/**
 *
 */
	public function offsetGet($offset) {
		$config = $this->_getConfigString();
		return isset($this->{$config}[$this->_activeConfig][$offset]) ? $this->{$config}[$this->_activeConfig][$offset] : null;
	}

}