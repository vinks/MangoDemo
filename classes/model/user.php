<?php

class Model_User extends Mango {

	protected $_fields = array(
		'role' => array(
			'type'     => 'enum',
			'values'   => array('viewer','contributor','manager','administrator','owner','specific'),
			'required' => TRUE
		),
		'email' => array(
			'type'       => 'string',
			'required'   => TRUE,
			'min_length' => 4,
			'max_length' => 127,
			'unique'     => TRUE,
		)
	);

	protected $_relations = array(
		'account'    => array('type'=>'belongs_to'),
		'blogs'      => array('type'=>'has_many'),
		'circles'    => array('type'=>'has_and_belongs_to_many','model'=>'group')
	);

	protected $_db = 'demo'; //don't use default db config
}