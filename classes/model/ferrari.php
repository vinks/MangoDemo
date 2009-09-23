<?php

class Model_Ferrari extends Model_Car {

	protected $_car_type = 2;

	// !! With extending classes - specify fields and relations in this method !!
	protected function set_model_definition()
	{
		// Specify the fields/relations specific for this class
		$this->_set_model_definition(array(
			'_fields' => array(
				'ferrari_data' => array('type'=>'string')
			)
		));

		parent::set_model_definition();
	}
}