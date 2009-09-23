<?php

class Model_Spyker extends Model_Car {

	protected $_car_type = 1;

	// !! With extending classes - specify columns and relations in this method !!
	protected function set_model_definition()
	{
		// Specify the columns/relations specific for this class
		$this->_set_model_definition(array(
			'_fields' => array(
				'spyker_data' => array('type'=>'string')
			)
		));

		parent::set_model_definition();
	}
}