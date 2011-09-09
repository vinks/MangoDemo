<?php

class Controller_MangoDemo extends Controller_Template {

	public function action_demo1()
	{
		// creating empty account object
		$account = Mango::factory('account', array(
			'name' => 'testaccount'
		))->create();
		Fire::log($account->as_array(), 'ACCOUNT');
		
		// now we can use the ID to retrieve it from DB
		$account2 = Mango::factory('account', array(
			'_id' => $account->_id
		))->load();
		Fire::log($account2->as_array(), 'ACCOUNT 2');
		
		// Clean up
		$account->delete();
	}
	
	public function action_demo2()
	{
		// creating account
		$account = Mango::factory('account',array(
			'name' => 'testaccount'
		))->create();
		Fire::log($account->as_array(), 'ACCOUNT');
		
		// simulate $_POST object
		$post = array(
			'email'      => 'user@user.com',
			'role'       => 'manager',
			'account_id' => $account->_id
		);
		
		// create empty user object
		$user = Mango::factory('user');
		
		try
		{
			// create user
			$user->values($post);
			
			if ($user->check())
				$user->create();
			
			// show user
			Fire::log($user->as_array(), 'USER');

			// load user by email
			$user2 = Mango::factory('user',array(
				'email' => 'user@domain.com'
			))->load();

			// this should be the same
			Fire::log($user2->as_array(), 'USER 2');
			// you can access the account from the user object
			Fire::log($user->account->as_array(), 'USER ACCOUNT');

			// and you can access the users from the account object
			$users = $account->users;
			Fire::log('account ' . $account->name . ' has ' . $users->count() .' users', 'DEBUG');
		}
		catch(Validation_Exception $e)
		{
			Fire::log($e->array->errors(), 'ERR');
		}
	}
	
	public function action_demo3()
	{
		// creating account
		$account = Mango::factory('account',array(
			'name' => 'testaccount'
		))->create();
		Fire::log($account->as_array(), 'ACCOUNT');

		// atomic update
		$account->name = 'name2';
		$account->some_counter->increment(5);
		$account->update();
		Fire::log($account->as_array(), 'ACCOUNT atomic update');

		// another update
		$account->some_counter->increment();
		$account->update();
		Fire::log($account->as_array(), 'ACCOUNT another update');

		$account->delete();
	}
	
	public function action_demo4()
	{
		// creating account
		$account = Mango::factory('account',array(
			'name' => 'testaccount'
		))->create();

		// create user
		$user = Mango::factory('user',array(
			'role' => 'manager',
			'email' => 'user@domain.com',
			'account_id' => $account->_id
		))->create();

		// create blog
		$blog = Mango::factory('blog',array(
			'title' => 'my first blog',
			'text' => 'hello world',
			'time_written' => time(),
			'user_id' => $user->_id
		))->create();

		// add an embedded has many object
		$comment = Mango::factory('comment',array(
			'name'    => 'John Doe',
			'comment' => 'Hello to you to',
			'time'    => time()
		));

		// to add a comment to blog (atomic) you can choose:
		$blog->add($comment); // OR $blog->comments[] = $comment;

		// save blog 
		$blog->update(); 

		// remove comment
		$blog->remove($comment); // OR unset($blog->comments[0]);

		// save blog
		$blog->update();

		// add comment again
		$blog->comments[] = $comment;

		// add another comment
		$comment2 = Mango::factory('comment',array(
			'name'    => 'Jane Doe',
			'comment' => 'I like your style',
			'time'    => time()
		));

		// add a second comment
		$blog->comments[] = $comment2; // or $blog->add($comment);

		$blog->update();

		// This will show the comments stored IN the blog object
		Fire::log($blog->as_array(), 'blog');

		// You can access the comments
		// $blog->comments->as_array() is also possible
		$i = 1;
		foreach($blog->comments as $comment)
		{
			Fire::log($comment->as_array(), 'comment' . $i);
			$i++;
		}

		// Reload
		$blog2 = Mango::factory('blog', array(
			'_id' => $blog->_id
		))->load();
		Fire::log($blog2->as_array(), 'blog 2');

		// Remove second comment
		unset($blog2->comments[1]);

		$blog2->update();

		Fire::log($blog2->as_array(), 'blog 2');

		// Clean up
		$account->delete();
	}
	
	public function action_demo5()
	{
		// creating account
		$account = Mango::factory('account',array(
			'name' => 'testaccount'
		))->create();

		// create user
		$user1 = Mango::factory('user',array(
			'role' => 'manager',
			'email' => 'user@domain.com',
			'account_id' => $account->_id
		))->create();

		$user2 = Mango::factory('user',array(
			'role' => 'manager',
			'email' => 'user2@domain.com',
			'account_id' => $account->_id
		))->create();

		$group1 = Mango::factory('group',array(
			'name' => 'Group1'
		))->create();

		$group2 = Mango::factory('group',array(
			'name' => 'Group2'
		))->create();

		// add HABTM relationship between users and groups
		$user1->add($group1);
		$user1->add($group2);
		$user2->add($group1);
		$user2->add($group2);

		//SAVE ALL OBJECTS
		$user1->update();
		$user2->update();
		$group1->update();
		$group2->update();
		
		Fire::log($user1->as_array(), '$user1->as_array()');
		Fire::log($group1->as_array(), '$group1->as_array()');

		// delete group 2 - this should remove references from both users
		$group2->delete();

		$user1->reload();
		$user2->reload();

		Fire::log($user1->as_array(), '$user1->as_array()');
		Fire::log($user2->as_array(), '$user2->as_array()');

		// Clean up - this should delete account -> thereby both users
		// and thereby clean user refs from group1
		$account->delete();

		// The $group1 object will still exist, although it's related $user is removed
		// lets check if the relationship is gone too:
		$group1->reload();

		Fire::log($group1->as_array(), '$group1->as_array()');

		$group1->delete();
		$group2->delete();
	}
	
	public function action_demo6()
	{
		// creating account
		$account = Mango::factory('account',array(
			'name' => 'testaccount'
		))->create();

		// this is atomic
		$account->categories[] = 'cat1';

		$account->update();

		// as is $account->categories->push('cat1');
		// this isn't (but is possible)
		// $account->categories = array('cat1');
		// $account->update();
		
		Fire::log($account->as_array(), '$account->as_array()');

		// try to push the same value
		$account->categories[] = 'cat1';
		
		Fire::log($account->as_array(), 'try to push the same value cat1');

		$account->categories[] = 'cat2';
		$account->update();

		Fire::log($account->as_array(), 'push value cat2');

		// atomic pull
		$account->categories->pull('cat1');
		// OR
		// unset($account->categories[ $account->categories->find('cat1') ]);
		$account->update();

		Fire::log($account->as_array(), 'atomic pull cat1');

		// Clean up
		$account->delete();
	}
	
	public function action_demo7()
	{
		/* Unsaved objects */
		
		// Counters
		$table   = array();
		$table[] = array('Account','Changed');
		
		$account = Mango::factory('account');
		$account->some_counter->increment();
		$table[] = array($account->as_array(), $account->changed(FALSE));
		
		$account = Mango::factory('account');
		$account->some_counter->decrement();
		$table[] = array($account->as_array(), $account->changed(FALSE));
		
		$account = Mango::factory('account');
		$account->some_counter = 5;
		$table[] = array($account->as_array(), $account->changed(FALSE));
		
		Fire::table('An unsaved object::Counters', $table);
		
		// Sets
		$table   = array();
		$table[] = array('Account','Changed');
		
		$account = Mango::factory('account');
		$account->categories[] = 'cat1';
		$table[] = array($account->as_array(), $account->changed(FALSE));
		
		$account = Mango::factory('account');
		$account->categories = array('cat1','cat2');
		$table[] = array($account->as_array(), $account->changed(FALSE));
		
		Fire::table('An unsaved object::Sets', $table);
		
		// Arrays
		$table   = array();
		$table[] = array('Account','Changed');
		
		$account = Mango::factory('account');
		$account->some_array[] = 'cat1';
		$table[] = array($account->as_array(), $account->changed(FALSE));
		
		$account = Mango::factory('account');
		$account->some_array['key'] = 'cat1';
		$table[] = array($account->as_array(), $account->changed(FALSE));
		
		$account = Mango::factory('account');
		$account->some_array = array('cat1','key'=>'bla');
		$table[] = array($account->as_array(), $account->changed(FALSE));
		
		Fire::table('An unsaved object::Arrays', $table);
		
		
		/* Saved objects */
		
		// Counters
		$table   = array();
		$table[] = array('Account','Changed');
		
		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->create();
		$account->some_counter->increment(); // this is atomic (uses $inc)
		$table[] = array($account->as_array(), $account->changed(TRUE));
		$account->delete();
		
		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->create();
		$account->some_counter = 5; // this is NOT atomic (uses $set, not $inc)
		$table[] = array($account->as_array(), $account->changed(TRUE));
		$account->delete();

		Fire::table('Saved objects::Counters', $table);
		
		// Sets
		$table   = array();
		$table[] = array('Account','Changed');
		
		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->create();
		$account->categories[] = 'cat1'; // this is atomic (uses $push)
		$table[] = array($account->as_array(), $account->changed(TRUE));
		$account->delete();

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->create();
		$account->categories = array('cat1','cat2'); // this is not atomic - a full reset of the categories array
		$table[] = array($account->as_array(), $account->changed(TRUE));
		$account->delete();
		
		Fire::table('Saved objects::Sets', $table);
		
		// Arrays
		$table   = array();
		$table[] = array('Account','Changed');
		
		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->create();
		$account->some_array[] = 'bla';
		$table[] = array($account->as_array(), $account->changed(TRUE));
		$account->delete();

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->create();
		$account->some_array['key'] = 'bla';
		$table[] = array($account->as_array(), $account->changed(TRUE));
		$account->delete();

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->create();
		$account->some_array = array('key' => 'bla', 'blo');
		$table[] = array($account->as_array(), $account->changed(TRUE));
		$account->delete();
		
		Fire::table('Saved objects::Arrays', $table);
	}
	
	public function action_demo8()
	{
		// let's simulate a load from DB
		$account = Mango::factory('account')->values(array(
			'_id'=>1,
			'name'=>'test',
			'report' => array(
				'total' => 5,
				'blog1' => array(
					'views' => 4,
					'comments'=> 3
				)
			)
		));

		// atomic counters are easy:
		$account->report['total']->increment();
		$account->report['blog1']['views']->increment();

		//$content .= Kohana::debug($account->changed(TRUE));

		// simulate changes were saved
		$account->saved();

		// !! the following isn't necessary anymore - you can implicitely create counters
		// from Mango_Arrays, even when they haven't been defined before !!

		//$account->report['blog2'] = array('views'=>0,'comments'=>0);

		// just create counters on the fly, no matter how deep and no matter
		// if the fields haven't been defined and/or set to counter
		$account->report['blog2']['views']->increment();

		// also update an existing counter
		$account->report['blog1']['views']->increment();
		
		Fire::log($account->changed(TRUE), 'ACCOUNT CHANGED ACCOUNT');

		// simulate save
		$account->saved();

		// do some more counting
		$account->report['blog2']['views']->increment();

		// and now it will inc
		Fire::log($account->changed(TRUE), 'ACCOUNT CHANGED ACCOUNT');
	}
	
	public function action_demo9()
	{
		// Note: extension support is useful if you have different classes, that inherit
		// from the same (base) class, but each have different additional columns.

		// Create a Spyker car object
		// We should have access to the Car_Model columns as well as the Spyker_Model columns
		$car = Mango::factory('spyker',array(
			'price' => 1000,
			'spyker_data' => 'hello'
		));

		// create
		$car->create();
		
		Fire::log($car->as_array(), 'CAR spyker');


		// Now create another car
		$car = Mango::factory('ferrari');
		$car->price = 750;
		$car->ferrari_data = 'world';
		$car->create();

		Fire::log($car->as_array(), 'CAR ferrari');

		// Now we have 2 cars saved in the cars collection, one ferrari, one spyker
		// Let's check - note we use 'car' in the factory method, but we get a fully
		// extended ferrari/spyker_model in return
		$cars = Mango::factory('car')->load(FALSE);
		foreach($cars as $car)
		{
			Fire::log($car->as_array(), 'CAR');

			// clean up
			$car->delete();
		}
	}
	
	public function action_demo10()
	{
		$blog = Mango::factory('blog');

		// Make some data invalid to see validation error

		$data = array(
			'title'        => 'Title',
			'text'         => 'texxt',
			'time_written' => time(),
			'user_id'      => 12,
			'comments'     => array(
				array(
					//'name'    => 'N1asa', // this title will now fail the min_length = 4 rule
					'comment' => 'c1',
					'time'    => time()
				),
				array(
					'name'    => 'N2asa',
					'comment' => 'c2',
					'time'    => time()
				)
			)
		);

		// Validating full document
		try
		{
			$data1 = $blog->check($data);
			Fire::log($data1, 'Full Validation success!');
		}
		catch(Validation_Exception $e)
		{
			$table   = array();
			$table[] = array('Model', 'Seq', 'Errors');
			$table[] = array($e->model, $e->seq,  $e->array->errors());
			Fire::table('Full Validation failed', $table);
		}

		// Validating local document only
		try
		{
			$data2 = $blog->check($data, Mango::CHECK_LOCAL);
			Fire::log($data2, 'Local Validation success!');
		}
		catch(Validation_Exception $e)
		{
			$table   = array();
			$table[] = array('Model', 'Seq', 'Errors');
			$table[] = array($e->model, $e->seq,  $e->array->errors());
			Fire::table('Full Validation failed', $table);
		}

		// Validating supplied fields only
		// will not fail on required fields because it won't check those,
		// it will fail however on the supplied and incorrect 'name' field of the first column
		$data = array(
			'title'        => 'Title',
			'comments'     => array(
				array(
					'name'    => 'N1', // this title will now fail the min_length = 4 rule
				),
				array(
					'name'    => 'N2asa',
				)
			)
		);

		try
		{
			$data1 = $blog->check($data, Mango::CHECK_ONLY);
			Fire::log($data1, 'Only Validation success!');
		}
		catch(Validation_Exception $e)
		{
			$table   = array();
			$table[] = array('Model', 'Seq', 'Errors');
			$table[] = array($e->model, $e->seq,  $e->array->errors());
			Fire::table('Only Validation failed', $table);
		}
	}

	public function action_demo11()
	{
		// create 2 different blog entries with different titles
		$blog1 = Mango::factory('blog', array(
			'title'        => 'title1',
			'text'         => 'text1',
			'time_written' => time()
		))->create();

		$blog2 = Mango::factory('blog', array(
			'title'        => 'title2',
			'text'         => 'text2',
			'time_written' => time()
		))->create();

		$db = $blog2->db();

		// use a multiple update to change title of all blogs at once
		$db->update('blogs', array(), array('$set'=> array('title'=>'title3')), array('multiple' => true));

		// check and delete
		foreach( Mango::factory('blog')->load(FALSE) as $blog)
		{
			Fire::log($blog->as_array(), 'BLOG');
			$blog->delete();
		}
	}

	public function action_demo12()
	{
		// Create blog
		$blog = Mango::factory('blog', array(
			'title'        => 'title1',
			'text'         => 'text1',
			'time_written' => time()
		))->create();

		// Unset a field
		unset($blog->title);

		Fire::log($blog->changed(true), 'Unset title');

		// Save
		$blog->update();

		// Reload blog
		$blog->reload();

		// Check if title is really missing
		Fire::log($blog->as_array(), 'Title should be missing');

		// Add 2 comments
		$blog->add( Mango::factory('comment', array(
			'name'    => 'Name1',
			'comment' => 'Text1',
			'time'    => time()
		)));

		$blog->add( Mango::factory('comment', array(
			'name'    => 'Name2',
			'comment' => 'Text2',
			'time'    => time()
		)));

		// This should use pushAll
		Fire::log($blog->changed(TRUE), 'Adding comments using $pushAll');

		$blog->update();
		$blog->reload();

		$blog->comments[0]->comment = 'New Text';

		Fire::log($blog->changed(TRUE), 'Update using array indices');

		$blog->update();
		$blog->reload();

		Fire::log($blog->changed(TRUE), 'New text in first comment');

		// Clean up
		$blog->delete();
	}
	
	public function action_demo13()
	{
		$user = Mango::factory('user', array(
			'role'  => 'viewer',
			'email' => 'a@b.nl'
		))->create();

		$group = Mango::factory('group', array(
			'name' => 'wouter'
		))->create();

		$user->add($group,'circle');

		Fire::log($user->changed(TRUE), 'USER CHANGED');
		Fire::log($group->changed(TRUE), 'GROUP CHANGED');

		$user->update();
		$group->update();

		$group->reload();
		$user->reload();

		foreach ( $group->users as $user)
		{
			Fire::log($user->as_array(FALSE), 'USER');
		}

		foreach ( $user->groups as $group)
		{
			Fire::log($group->as_array(FALSE), 'GROUP');
		}

		$group->remove($user);
		
		Fire::log($user->changed(TRUE), 'USER CHANGED');
		Fire::log($group->changed(TRUE), 'GROUP CHANGED');

		$user->delete();
		$group->delete();
	}
}
