<?php

class Model_Group extends Mango {

	protected $_fields = array(
	 	'name'       => array('type'=>'string','required'=>TRUE,'min_length'=>4,'max_length'=>127,'rules'=>array('alpha_numeric'=>NULL))
	);

	protected $_relations = array(
		'users' => array('type' => 'has_and_belongs_to_many')
	);

	protected $_db = 'demo'; //don't use default db config
}