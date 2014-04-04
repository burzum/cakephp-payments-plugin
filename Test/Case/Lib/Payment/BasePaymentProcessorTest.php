<?php
App::uses('CakeTestCase', 'TestSuite');
App::uses('BasePaymentProcessor', 'Payments.Lib/Payment');

class BasePaymentProcessorClass extends BasePaymentProcessor {

	public function setFieldRules($fields) {
		$this->_fieldRules = $fields;
	}

	public function cancel($paymentReference, array $options = array()) {

	}

	public function notificationCallback(array $options = array()) {

	}

	public function pay($amount, array $options = array()) {

	}

	public function refund($paymentReference, $amount, $comment = '', array $options = array()) {

	}
}

/**
 * BasePaymentProcessorTest
 *
 * @author Florian Kramer
 * @copyright 2012 Florian Kramer
 * @license MIT
 */
class BasePaymentProcessorTest extends CakeTestCase {

	protected $_fieldRules = array(
		'pay' => array(
			'amount' => array(
				'required' => true,
				'type' => array('int')
			),
		),
	);

	protected $_orFieldRules = array(
		'pay' => array(
			'OR' => array(
				'amount' => array(
					'required' => true,
					'type' => array('int')
				),
				'description' => array(
					'required' => true,
					'type' => array('string')
				),
			),
		),
	);

/**
 * startTest
 *
 * @return void
 */
	public function setUp() {
		$this->BasePaymentProcessor = new BasePaymentProcessorClass(array());
	}

/**
 * endTest
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->BasePaymentProcessor);
		ClassRegistry::flush();
	}

	public function testValidateFields() {
		$this->assertTrue($this->BasePaymentProcessor->validateFields('actionDoesNotExist'));

		$this->BasePaymentProcessor->setFieldRules($this->_fieldRules);
		$this->expectException('PaymentProcessorException');
		$this->BasePaymentProcessor->validateFields('pay');
	}

	public function testValidateFieldsType() {
		$this->BasePaymentProcessor->setFieldRules($this->_fieldRules);
		$this->BasePaymentProcessor->set('amount', 'data');
		$this->expectException('PaymentProcessorException');
		$this->BasePaymentProcessor->validateFields('pay');
	}

	public function testValidateFieldsOk() {
		$this->BasePaymentProcessor->setFieldRules($this->_fieldRules);
		$this->BasePaymentProcessor->set('amount', 100);
		$this->assertTrue($this->BasePaymentProcessor->validateFields('pay'));
	}

	public function testValidateORFields() {
		$this->BasePaymentProcessor->setFieldRules($this->_orFieldRules);
		$this->BasePaymentProcessor->set('description', 0);
		$this->expectException('PaymentProcessorException');
		$this->BasePaymentProcessor->validateFields('pay');
	}

	public function testValidateORFieldsOk() {
		$this->BasePaymentProcessor->setFieldRules($this->_orFieldRules);
		$this->BasePaymentProcessor->set('description', 'test');
		$this->assertTrue($this->BasePaymentProcessor->validateFields('pay'));
		$this->BasePaymentProcessor->set('description', false);
		$this->BasePaymentProcessor->set('amount', 100);
		$this->assertTrue($this->BasePaymentProcessor->validateFields('pay'));
		$this->BasePaymentProcessor->set('description', 'description');
		$this->BasePaymentProcessor->set('amount', 100);
		$this->assertTrue($this->BasePaymentProcessor->validateFields('pay'));
	}
}