<?php
/**
 * PaymentApiLogFixture
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
class PaymentApiLogFixture extends CakeTestFixture {

/**
 * Name
 *
 * @var string $name
 */
	public $name = 'PaymentApiLog';

/**
 * Table
 *
 * @var array $table
 */
	public $table = 'payment_api_logs';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
		'order_id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 36),
		'token' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'processor' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'type' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'message' => array('type'=>'text', 'null' => false, 'default' => NULL),
		'file' => array('type'=>'text', 'null' => true, 'default' => NULL),
		'line' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 6),
		'trace' => array('type'=>'text', 'null' => true, 'default' => NULL),
		'post' => array('type'=>'text', 'null' => true, 'default' => NULL),
		'get' => array('type'=>'text', 'null' => true, 'default' => NULL),
		'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'ORDER_INDEX' => array('column' => 'order_id')),
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
	);

} 
