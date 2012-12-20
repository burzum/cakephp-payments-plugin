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
			throw new RuntimeException('Curl is not installed!');
		}

		$this->init();
		$this->buildCurlConstants();
	}

/**
 *
 */
	protected function buildCurlConstants() {
		$constants = get_defined_constants(true);
		foreach ($constants['curl'] as $key => $value) {
			if (strpos($key, 'CURLOPT_') === 0) {
				$key = str_ireplace(array('CURLOPT', '_'), '', $key);
				$this->_curlConstants[$key] = $value;
			}
		}
	}

/**
 * Inits the curl handler
 *
 * @return void
 */
	public function init() {
		$this->_handler = curl_init();
	}

	public function getHandler() {
		return $this->_handler;
	}

	public function post($uri = null, $data = array(), $request = array()) {
		return $this->request(array_merge(array('method' => 'POST', 'uri' => $uri, 'body' => $data), $request));
	}

	public function setOption($option, $value) {
		$option = strtoupper($option);
		if (!isset($this->_curlConstants[$option])) {
			throw new InvalidArgumentException(sprintf('Invalid cURL option %s!', $option));
		}

		curl_setopt($this->_handler, $this->_curlConstants[$option], $value);
	}

/**
 * Returns the last error code and message
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

		if ($request['method'] === 'POST') {
			if (is_array($request['body'])) {
				$request['body'] = http_parse_params($request['body']);
			}
			$this->setOption('POSTFIELDS', $request['body']);
			$this->setOption('POST', 1);
		}

		return curl_exec($this->_handler);
	}

	public function reset() {
		curl_close($this->_handler);
		$this->init();
	}

}