<?php

class Model_Comment extends Mango {

	protected $_embedded = TRUE;

	protected $_fields = array(
	 	'name'         => array('type'=>'string','required'=>TRUE,'min_length'=>4,'max_length'=>127),
	 	'comment'      => array('type'=>'string','required'=>TRUE),
		'time'         => array('type'=>'int','required'=>TRUE)
	);

	protected $_db = 'demo'; //don't use default db config
}