<?php
class PaymentApiLogsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'payment_api_logs' => array(
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
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'payment_api_log',
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
