<?php

class Model_Blog extends Mango {

	protected $_relations = array(
		'user'         => array('type'=>'belongs_to')
	);
	
	protected $_fields = array(
	 	'title'        => array('type'=>'string','required'=>TRUE,'min_length'=>4,'max_length'=>127),
	 	'text'         => array('type'=>'string','required'=>TRUE),
		'time_written' => array('type'=>'int','required'=>TRUE),
		'time_post'    => array('type'=>'int'),
		'comments'     => array('type'=>'has_many')
	);

	protected $_db = 'demo'; //don't use default db config
}