<?php
	
	//require slim and load it
	require 'Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	
	//create an instance of the SLIM
	$app = new \Slim\Slim();
	
	//Main method get
	$app->get('/', function () use ($app){
		echo "Our first REST API";
	});
	
	//Method post to add a user
	$app->post('/user', function() use ($app){		

		//capture the parameters sent by our form client
		$name = $app->request->params('name');		
		$email = $app->request->params('email');		
		$password = $app->request->params('password');
		
		//convert to string json
		$user = array('name' => $name, 'email' => $email, 'password' => $password);
		$json = json_encode($user);		
		
		//save the user in a txt file
		$fp = fopen ("users.txt", "w");		
		fwrite($fp, $json);		
		fclose($fp);
	});
	
	//Method get to get an user
	$app->get('/user', function () use ($app) {
		$json = file("users.txt");				
		$app->response->setBody($json[0]);		
	});
	
	//execute our API
	$app->run();
?>