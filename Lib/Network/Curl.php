<?php
/**
 * Curl wrapper class
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
class Curl {

/**
 * Curl handler
 *
 * @var resource
 */
	protected $_handler = null;

/**
 * Curl constants mapping
 *
 * @var array
 */
	protected $_curlConstants = array();

/**
 * Constructor
 *
 * @throws RuntimeException
 * @return Curl
 */
	public function __construct() {
		if (!function_exists('curl_init')) {
			throw new RuntimeException('cURL is not installed!');
		}

		$this->init();
		$this->_buildCurlConstants();
	}

/**
 * Builds a list of cURL constant names in an array
 *
 * @return void
 */
	protected function _buildCurlConstants() {
		$constants = get_defined_constants(true);
		foreach ($constants['curl'] as $key => $value) {
			if (strpos($key, 'CURLOPT_') === 0) {
				$key = str_ireplace(array('CURLOPT', '_'), '', $key);
				$this->_curlConstants[$key] = $value;
			}
		}
	}

/**
 * Initializes the curl handler
 *
 * @return void
 */
	public function init() {
		$this->_handler = curl_init();
	}

/**
 * Public interface to get the cURL handler
 *
 * @return null|resource
 */
	public function getHandler() {
		return $this->_handler;
	}

/**
 * Prepares and executes a POST request
 *
 * @param
 * @param array $data
 * @param array $request
 * @return mixed
 */
	public function post($uri = null, $data = array(), $request = array()) {
		return $this->request(array_merge(array('method' => 'POST', 'uri' => $uri, 'body' => $data), $request));
	}

/**
 * Sets a cURL option
 *
 * @param string $option Name of the cURL option without the CURLOPT prefix
 * @param mixed $value
 * @return void
 * @throws InvalidArgumentException Is thrown when the cURL option does not exist
 */
	public function setOption($option, $value) {
		$option = strtoupper(str_replace('_', '', $option));
		if (!isset($this->_curlConstants[$option])) {
			throw new InvalidArgumentException(sprintf('Invalid cURL option %s!', $option));
		}

		curl_setopt($this->_handler, $this->_curlConstants[$option], $value);
	}

/**
 * Returns the last error code and message
 *
 * @return array
 */
	public function getLastError() {
		return array(
			'code' => curl_errno($this->_handler),
			'message' => curl_error($this->_handler));
	}

/**
 * @todo finish me
 */
	public function request($request) {
		$this->setOption('URL', $request['uri']);

		if (is_array($request['body'])) {
			$request['body'] = http_build_query($request['body']);
		}

		if ($request['method'] === 'POST') {
			$this->setOption('POSTFIELDS', $request['body']);
			$this->setOption('POST', 1);
		}

		return curl_exec($this->_handler);
	}

/**
 * Closes the current cURL handler and initializes it again
 *
 * @return void
 */
	public function reset() {
		curl_close($this->_handler);
		$this->init();
	}

}