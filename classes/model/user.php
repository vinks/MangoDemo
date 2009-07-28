<?php

class Model_User extends Mango {

	protected $_belongs_to = array('account');
	protected $_has_many = array('blogs');
	protected $_has_and_belongs_to_many = array('groups');

	protected $_columns = array(
	 	'role'       => array('type'=>'enum','values'=>array('viewer','contributor','manager','administrator','owner','specific')),
	 	'email'      => array('type'=>'string')
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('email', array(
				'required'  => NULL,
				'length'    => array(4,127),
				'email'     => NULL,
			))
			->callback('email',array($this,'is_unique'))
			->rule('role','required')
			->rule('role','in_array',array(array('viewer','contributor','manager','administrator','owner','specific')))
			->rule('account_id','required');

		return parent::validate($array, $save);
	}

	public function is_unique(Validate $validate, $field)
	{
		if ($this->_loaded AND $this->_object['email'] === $array[$field])
		{
			// This value is unchanged
			return TRUE;
		}

		if($this->_db->find_one($this->_collection_name,array('email' => $array[$field] ) ) !== NULL)
		{
			$validate->error($field,'is_unique');
		}
	}

	// Allows users to be loaded by email.
	public function unique_criteria($id)
	{
		if ( ! empty($id) AND is_string($id) )
			return array('email'=>$id);

		return parent::unique_criteria($id);
	}
}