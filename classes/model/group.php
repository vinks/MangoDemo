<?php

class Model_Group extends Mango {

	protected $_has_and_belongs_to_many = array('users');

	protected $_columns = array(
	 	'name'       => array('type'=>'string')
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validate::factory($array)
			->filter(TRUE,'trim')
			->rules('name', array(
				'required' => NULL,
				'length'   => array(4,127),
				'alpha_numeric' => NULL
			));

		return parent::validate($array, $save);
	}
}