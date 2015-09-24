<?php
	include_once 'User.class.php';
	
	$user = new User('John', 'john@oop.com', 25);
	
	$user->show();
	
	$user->changeName('Milton');
	
	$user->show();
?>