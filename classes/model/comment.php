<?php

class Model_Comment extends Mango {

	protected $_embedded = TRUE;

	protected $_columns = array(
	 	'name'         => array('type'=>'string'),
	 	'comment'      => array('type'=>'string'),
		'time'         => array('type'=>'int')
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array & $array)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rule('name','required')
			->rule('name','length',array(4,127))
			->rule('comment','required')
			->rule('time','required');

		return parent::validate($array, FALSE);
	}
}