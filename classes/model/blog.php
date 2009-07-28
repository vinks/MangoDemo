<?php

class Model_Blog extends Mango {
	
	protected $_belongs_to = array('user');
	
	protected $_columns = array(
	 	'title'        => array('type'=>'string'),
	 	'text'         => array('type'=>'string'),
		'time_written' => array('type'=>'int'),
		'time_post'    => array('type'=>'int'),
		'comments'     => array('type'=>'has_many')
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array $array, $save = FALSE)
	{
		$array = Validate::factory($array)
			// Trim all fields
			->filter(TRUE, 'trim')
			->rule('title', 'required')
			->rule('title', 'length', array(4,127))
			->rule('text', 'required')
			->rule('time_written','required');

		return parent::validate($array, $save);
	}
}