<?php
	
	require_once 'model/User.class.php';
    require_once 'controller/UserController.class.php';
	
	//require slim and load it
	require 'Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	
	//create an instance of the SLIM
	$app = new \Slim\Slim();
	
	//Main method
	$app->get('/', function () use ($app){
		echo "REST API with SLIM";
	});
	
	//Method to add a user
	$app->put('/user/add', function() use ($app){		

		//capture the parameters sent by our form client
		$user = new User();
		$user->setName($app->request->params('name'));		
		$user->setEmail($app->request->params('email'));	
		
		//encrypt the password
		$password = password_hash($app->request->params('password'), PASSWORD_DEFAULT);
		
		$user->setPassword($password);
		
        $userCtrl = new UserController();
		$result = $userCtrl->save($user);
        
        if(!$result){
            //HTTP status 409 - Conflict, this occurs when already exist a user.
            $app->response->status(409);
        }
        
	});

    //Method to update a user
	$app->post('/user/update', function() use ($app){		

		//capture the parameters sent by our form client
		$user = new User();
		$user->setName($app->request->params('name'));		
		$user->setEmail($app->request->params('email'));	
		
		//encrypt the password
		$password = password_hash($app->request->params('password'), PASSWORD_DEFAULT);
		
		$user->setPassword($password);
		
        $userCtrl = new UserController();
		$result = $userCtrl->update($user);
        
        if(!$result){
            //HTTP status 409 - Conflict, this occurs when user was not found.
            $app->response->status(409);
        }
	});

    //Method to delete a user
    $app->delete('/user/delete', function() use ($app){
		$email = $app->request->params('email');
        $userCtrl = new UserController();        
        $result = $userCtrl->delete($email);
        
        if(!$result){
            //HTTP status 500 - Internal server error, this occurs when can't delete a user.
            $app->response->status(500);
			echo "We can't delete user";
        }else{
			echo "User deleted successfully";
		}
    });
	
	//Method to get a user
	$app->get('/user', function () use ($app) {
        $email = $app->request->params('email');
        
		$userCtrl = new UserController();
        $user = $userCtrl->getByEmail($email); 
        
        if(!$user){
            //HTTP status 404 - User not found
            $app->response->status(404);
        }
        
		$app->response->setBody($user);
	});

    //Method to get all user
	$app->get('/user/all', function () use ($app) {
		$userCtrl = new UserController();
        $users = $userCtrl->getAllUser();
                       
        if(!$users){
            //HTTP status 404 -  no User found
            $app->response->status(404);
        }
        
		$app->response->setBody($users);		
	});
	
	//execute our API
	$app->run();
?>