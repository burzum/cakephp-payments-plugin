<?php
App::uses('PaymentProcessorConfig', 'Payments.Lib/Payment');
/**
 * PaymentProcessorConfigTest
 */
class PaymentProcessorConfigTest extends CakeTestCase {

	public function testConfig() {
		$Config = new PaymentProcessorConfig(array('test' => 'test'));
		$this->assertFalse($Config->sandboxMode());
		debug($Config['test']);
	}

}